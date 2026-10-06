<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class InfoController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Info/Index', [
            'topics' => $this->topics(),
        ]);
    }

    public function show(string $slug): Response
    {
        $topic = config("informacoes.$slug");

        abort_if($topic === null, 404);

        return Inertia::render('Info/Show', [
            'topic' => ['slug' => $slug, ...$topic],
            'topics' => $this->topics(),
        ]);
    }

    /**
     * @return list<array{slug: string, title: string, icon: string}>
     */
    private function topics(): array
    {
        return collect(config('informacoes'))
            ->map(fn (array $topic, string $slug) => [
                'slug' => $slug,
                'title' => $topic['title'],
                'icon' => $topic['icon'],
            ])
            ->values()
            ->all();
    }
}
