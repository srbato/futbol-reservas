<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\Reservation;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class VenueController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'zone' => ['nullable', 'string', 'max:120'],
            'sport' => ['nullable', 'in:football,padel,tennis,basketball,volleyball'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
            'date' => ['nullable', 'date'],
            'available_at' => ['nullable', 'date_format:H:i'],
        ]);

        $q = $validated['q'] ?? null;
        $zone = $validated['zone'] ?? null;
        $sport = $validated['sport'] ?? null;
        $minPrice = $validated['min_price'] ?? null;
        $maxPrice = $validated['max_price'] ?? null;
        $date = $validated['date'] ?? null;
        $availableAt = $validated['available_at'] ?? null;

        $zones = Venue::query()
            ->where('is_active', true)
            ->withActiveOwner()
            ->whereNotNull('zone')
            ->where('zone', '!=', '')
            ->select('zone')
            ->distinct()
            ->orderBy('zone')
            ->pluck('zone');

        $venuesQuery = Venue::query()
            ->where('is_active', true)
            ->withActiveOwner()

            ->when($q, function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('name', 'like', "%{$q}%")
                        ->orWhere('description', 'like', "%{$q}%")
                        ->orWhere('zone', 'like', "%{$q}%");
                });
            })

            ->when($zone, fn ($query) => $query->where('zone', $zone))

            ->when($sport, function ($query) use ($sport) {
                $query->whereHas('fields', function ($qf) use ($sport) {
                    $qf->where('is_active', true)
                        ->where('sport', $sport);
                });
            })

            // Si NO hay fecha, filtramos por precio base en SQL
            ->when(($minPrice !== null && $minPrice !== '') && !$date, function ($query) use ($minPrice) {
                $query->whereHas('fields.price', function ($qp) use ($minPrice) {
                    $qp->where('price_per_slot', '>=', $minPrice);
                });
            })

            ->when(($maxPrice !== null && $maxPrice !== '') && !$date, function ($query) use ($maxPrice) {
                $query->whereHas('fields.price', function ($qp) use ($maxPrice) {
                    $qp->where('price_per_slot', '<=', $maxPrice);
                });
            })

            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->orderBy('name');

        $venues = $venuesQuery->get([
            'id',
            'name',
            'description',
            'cover_image_path',
            'address',
            'zone',
            'lat',
            'lng'
        ]);

        $shouldFilterByEffectivePrice = $date && (
            ($minPrice !== null && $minPrice !== '') ||
            ($maxPrice !== null && $maxPrice !== '')
        );

        $shouldFilterByAvailability = $date && $availableAt;

        if ($shouldFilterByEffectivePrice || $shouldFilterByAvailability) {
            $selectedDate = Carbon::parse($date)->startOfDay();

            $venues->load([
                'fields' => function ($query) {
                    $query->where('is_active', true)->with([
                        'price',
                        'schedules',
                        'exceptions',
                        'blocks',
                        'discounts' => function ($q) {
                            $q->where('is_active', true);
                        },
                    ]);
                },
            ]);

            $reservationsByField = collect();

            if ($shouldFilterByAvailability) {
                $fieldIds = $venues->pluck('fields')
                    ->flatten()
                    ->pluck('id')
                    ->unique()
                    ->values();

                $reservationsByField = Reservation::query()
                    ->whereIn('field_id', $fieldIds)
                    ->whereIn('status', ['PAID', 'PENDING_PAYMENT'])
                    ->where(function ($q) {
                        $q->where('status', '!=', 'PENDING_PAYMENT')
                            ->orWhere(function ($q2) {
                                $q2->where('status', 'PENDING_PAYMENT')
                                    ->whereNotNull('expires_at')
                                    ->where('expires_at', '>', now());
                            });
                    })
                    ->whereDate('start_at', $selectedDate->toDateString())
                    ->get()
                    ->groupBy('field_id');
            }

            $venues = $venues
                ->filter(function ($venue) use (
                    $selectedDate,
                    $availableAt,
                    $minPrice,
                    $maxPrice,
                    $shouldFilterByEffectivePrice,
                    $shouldFilterByAvailability,
                    $reservationsByField
                ) {
                    return $this->venueMatchesAdvancedFilters(
                        $venue,
                        $selectedDate,
                        $availableAt,
                        $minPrice,
                        $maxPrice,
                        $shouldFilterByEffectivePrice,
                        $shouldFilterByAvailability,
                        $reservationsByField
                    );
                })
                ->values();
        }

        $favoriteVenueIds = Auth::check()
            ? Auth::user()->favoriteVenues()->pluck('venues.id')->toArray()
            : [];

        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $topReservedVenues = Venue::query()
            ->where('is_active', true)
            ->withActiveOwner()
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->withCount([
                'reservations as weekly_reservations_count' => function ($q) use ($startOfWeek, $endOfWeek) {
                    $q->whereBetween('start_at', [$startOfWeek, $endOfWeek])
                        ->whereIn('status', ['PAID', 'PENDING_PAYMENT']);
                }
            ])
            ->orderByDesc('weekly_reservations_count')
            ->get()
            ->filter(fn ($venue) => $venue->weekly_reservations_count > 0)
            ->take(8)
            ->values();

        $discountedVenues = Venue::query()
            ->where('is_active', true)
            ->withActiveOwner()
            ->whereHas('fields.discounts', function ($q) {
                $q->where('is_active', true);
            })
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->with(['fields.discounts' => function ($q) {
                $q->where('is_active', true);
            }])
            ->take(8)
            ->get();

        $bestRatedVenues = Venue::query()
            ->where('is_active', true)
            ->withActiveOwner()
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->orderByDesc('reviews_avg_rating')
            ->get()
            ->filter(fn ($venue) => $venue->reviews_count > 0)
            ->take(8)
            ->values();

        $favorites = auth()->check()
            ? auth()->user()->favoriteVenues()->where('is_active', true)->orderBy('name')->get()
            : collect();

        $hasFilters = (bool) ($q || $zone || $sport || ($minPrice !== null && $minPrice !== '') || ($maxPrice !== null && $maxPrice !== '') || $date || $availableAt);

        $allVenues = Venue::query()
            ->where('is_active', true)
            ->withActiveOwner()
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->orderBy('name')
            ->get(['id', 'name', 'description', 'cover_image_path', 'address', 'zone', 'lat', 'lng']);

        return view('venues.index', compact(
            'venues',
            'allVenues',
            'hasFilters',
            'zones',
            'q',
            'zone',
            'sport',
            'minPrice',
            'maxPrice',
            'date',
            'availableAt',
            'favoriteVenueIds',
            'favorites',
            'topReservedVenues',
            'discountedVenues',
            'bestRatedVenues'
        ));
    }

    public function show(Venue $venue)
    {
        // Si el dueño no tiene suscripción activa, el complejo no está disponible
        $venue->loadMissing('owner');
        $owner = $venue->owner;
        if ($owner && $owner->role !== 'super_admin' && !$owner->hasActiveVenueAdminSubscription()) {
            abort(404);
        }

        $venue->load([
            'fields' => fn($q) => $q->where('is_active', true),
            'fields.price',
            'reviews.user',
        ]);

        $averageRating = round($venue->reviews->avg('rating') ?? 0, 1);

        return view('venues.show', compact('venue', 'averageRating'));
    }

    private function venueMatchesAdvancedFilters(
        Venue $venue,
        Carbon $date,
        ?string $availableAt,
        $minPrice,
        $maxPrice,
        bool $shouldFilterByEffectivePrice,
        bool $shouldFilterByAvailability,
        Collection $reservationsByField
    ): bool {
        foreach ($venue->fields as $field) {
            $matchesPrice = !$shouldFilterByEffectivePrice
                || $this->fieldMatchesEffectivePriceFilter($field, $date, $availableAt, $minPrice, $maxPrice);

            $matchesAvailability = !$shouldFilterByAvailability
                || $this->fieldHasAvailableSlotAt($field, $date, $availableAt, $reservationsByField);

            if ($matchesPrice && $matchesAvailability) {
                return true;
            }
        }

        return false;
    }

    private function fieldMatchesEffectivePriceFilter(
        Field $field,
        Carbon $date,
        ?string $availableAt,
        $minPrice,
        $maxPrice
    ): bool {
        $basePrice = (float) ($field->price?->price_per_slot ?? 0);

        $candidatePrices = [$basePrice];

        foreach ($field->discounts as $discount) {
            if (!$discount->is_active) {
                continue;
            }

            if (!$this->discountMatchesDateAndTime($discount, $date, $availableAt, (int) $field->slot_minutes)) {
                continue;
            }

            $candidatePrices[] = (float) $discount->discount_price;
        }

        foreach ($candidatePrices as $price) {
            if ($minPrice !== null && $minPrice !== '' && $price < (float) $minPrice) {
                continue;
            }

            if ($maxPrice !== null && $maxPrice !== '' && $price > (float) $maxPrice) {
                continue;
            }

            return true;
        }

        return false;
    }

    private function discountMatchesDateAndTime(
        $discount,
        Carbon $date,
        ?string $availableAt,
        int $slotMinutes
    ): bool {
        if ($discount->date && $discount->date->toDateString() !== $date->toDateString()) {
            return false;
        }

        if (!$discount->date && !is_null($discount->day_of_week) && (int) $discount->day_of_week !== (int) $date->dayOfWeek) {
            return false;
        }

        // Si no se especificó hora en el filtro, alcanza con que el descuento aplique en algún momento de ese día
        if (!$availableAt) {
            return true;
        }

        // Si no tiene franja horaria, aplica todo el día
        if (!$discount->start_time || !$discount->end_time) {
            return true;
        }

        $slotStart = Carbon::parse($date->toDateString() . ' ' . $availableAt);
        $slotEnd = $slotStart->copy()->addMinutes($slotMinutes);

        $discountStart = Carbon::parse($date->toDateString() . ' ' . $discount->start_time);
        $discountEnd = Carbon::parse($date->toDateString() . ' ' . $discount->end_time);

        return $discountStart < $slotEnd && $discountEnd > $slotStart;
    }

    private function fieldHasAvailableSlotAt(
        Field $field,
        Carbon $date,
        string $availableAt,
        Collection $reservationsByField
    ): bool {
        if (!$field->is_active) {
            return false;
        }

        $dow = (int) $date->dayOfWeek;

        $exception = $field->exceptions->first(function ($exception) use ($date) {
            return Carbon::parse($exception->date)->toDateString() === $date->toDateString();
        });

        if ($exception?->is_closed) {
            return false;
        }

        $schedule = $field->schedules->firstWhere('day_of_week', $dow);

        $openTime = $exception?->open_time ?? $schedule?->open_time;
        $closeTime = $exception?->close_time ?? $schedule?->close_time;

        if (!$openTime || !$closeTime) {
            return false;
        }

        $slotStart = Carbon::parse($date->toDateString() . ' ' . $availableAt);
        $slotEnd = $slotStart->copy()->addMinutes((int) $field->slot_minutes);

        $open = Carbon::parse($date->toDateString() . ' ' . $openTime);
        $close = Carbon::parse($date->toDateString() . ' ' . $closeTime);

        if ($slotStart->lt($open) || $slotEnd->gt($close)) {
            return false;
        }

        if ($slotStart->lessThan(now())) {
            return false;
        }

        $blocked = $field->blocks->first(function ($block) use ($date, $slotStart, $slotEnd) {
            if (Carbon::parse($block->date)->toDateString() !== $date->toDateString()) {
                return false;
            }

            $blockStart = Carbon::parse($date->toDateString() . ' ' . $block->start_time);
            $blockEnd = Carbon::parse($date->toDateString() . ' ' . $block->end_time);

            return $blockStart < $slotEnd && $blockEnd > $slotStart;
        });

        if ($blocked) {
            return false;
        }

        $fieldReservations = $reservationsByField->get($field->id, collect());

        $occupied = $fieldReservations->first(function ($reservation) use ($slotStart, $slotEnd) {
            return $reservation->start_at < $slotEnd && $reservation->end_at > $slotStart;
        });

        if ($occupied) {
            return false;
        }

        return true;
    }
}