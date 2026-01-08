@php($title = __('common.loyalty_points'))
@include('layouts.app', [
    'title' => $title,
    'slot' => view('store.partials.points', [
        'userPoints' => $userPoints ?? 0,
        'userBalance' => $userBalance ?? 0,
        'pointValue' => $pointValue ?? 0,
        'pointsValue' => $pointsValue ?? 0,
        'currentTier' => $currentTier ?? null,
        'nextTier' => $nextTier ?? null,
        'progressToNext' => $progressToNext ?? 0,
        'pointsToNext' => $pointsToNext ?? 0,
        'rewards' => $rewards ?? collect(),
        'history' => $history ?? collect(),
        'tiers' => $tiers ?? collect(),
    ]),
])
