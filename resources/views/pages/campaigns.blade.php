@php($title = 'الحملات الإعلانية')
@include('layouts.admin', [
    'title' => $title,
    'slot' => view('pages.partials.campaigns', [
        'totalCampaigns' => $totalCampaigns ?? 0,
        'activeCampaigns' => $activeCampaigns ?? 0,
        'recentCampaigns' => $recentCampaigns ?? collect(),
    ]),
])


