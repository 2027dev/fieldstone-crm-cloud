<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class LeadFeatureController extends Controller
{
    /**
     * LeadBooster add-ons that are on the roadmap.
     *
     * @var array<string, array{title: string, icon: string, description: string}>
     */
    public const FEATURES = [
        'live-chat' => ['title' => 'Live Chat', 'icon' => 'chat', 'description' => 'Talk to website visitors in real time and turn conversations into leads.'],
        'chatbot' => ['title' => 'Chatbot', 'icon' => 'bot', 'description' => 'Qualify visitors around the clock with an automated conversation flow.'],
        'prospector' => ['title' => 'Prospector', 'icon' => 'binoculars', 'description' => 'Search a database of companies and people to find new prospects.'],
        'web-visitors' => ['title' => 'Web Visitors', 'icon' => 'radar', 'description' => 'See which companies visit your website and follow up while interest is high.'],
        'linkedin' => ['title' => 'LinkedIn', 'icon' => 'linkedin', 'description' => 'Add and enrich leads straight from LinkedIn profiles.'],
    ];

    public function __invoke(string $feature): View
    {
        return view('leads.feature', ['feature' => self::FEATURES[$feature], 'key' => $feature]);
    }
}
