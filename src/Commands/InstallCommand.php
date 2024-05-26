<?php

namespace vityakut\ProxyApi\Commands;

use Illuminate\Console\Command;
use vityakut\ProxyApi\ServiceProvider;
use vityakut\ProxyApi\Support\View;

class InstallCommand extends Command
{
    private const LINKS = [
        'Repository' => 'https://github.com/vityakut\proxyapi',
        'OpenAI PHP Docs' => 'https://github.com/openai-php/client#readme',
    ];

    protected $signature = 'proxyapi:install';

    protected $description = 'Prepares the ProxyApi client for use.';

    public function handle(): void
    {
        View::renderUsing($this->output);

        View::render('components.badge', [
            'type' => 'INFO',
            'content' => 'Installing ProxyApi for Laravel.',
        ]);

        $this->copyConfig();

        View::render('components.new-line');

        $this->addEnvKeys('.env');
        $this->addEnvKeys('.env.example');

        View::render('components.new-line');

        $wantsToSupport = $this->askToStarRepository();

        $this->showLinks();

        View::render('components.badge', [
            'type' => 'INFO',
            'content' => 'Open your .env and add your ProxyAPI key',
        ]);

        if ($wantsToSupport) {
            $this->openRepositoryInBrowser();
        }
    }

    private function copyConfig(): void
    {
        if (file_exists(config_path('proxyapi.php'))) {
            View::render('components.two-column-detail', [
                'left' => 'config/proxyapi.php',
                'right' => 'File already exists.',
            ]);

            return;
        }

        View::render('components.two-column-detail', [
            'left' => 'config/proxyapi.php',
            'right' => 'File created.',
        ]);

        $this->callSilent('vendor:publish', [
            '--provider' => ServiceProvider::class,
        ]);
    }

    private function addEnvKeys(string $envFile): void
    {
        $fileContent = file_get_contents(base_path($envFile));

        if ($fileContent === false) {
            return;
        }

        if (str_contains($fileContent, 'PROXYAPI_API_KEY')) {
            View::render('components.two-column-detail', [
                'left' => $envFile,
                'right' => 'Variables already exists.',
            ]);

            return;
        }

        file_put_contents(base_path($envFile), PHP_EOL.'PROXYAPI_API_KEY='.PHP_EOL, FILE_APPEND);

        View::render('components.two-column-detail', [
            'left' => $envFile,
            'right' => 'PROXYAPI_API_KEY variables added.',
        ]);
    }

    private function askToStarRepository(): bool
    {
        if (! $this->input->isInteractive()) {
            return false;
        }

        return $this->confirm(' <options=bold>Wanna show ProxyApi for Laravel some love by starring it on GitHub?</>', false);
    }

    private function openRepositoryInBrowser(): void
    {
        if (PHP_OS_FAMILY == 'Darwin') {
            exec('open https://github.com/vityakut/proxyapi');
        }
        if (PHP_OS_FAMILY == 'Windows') {
            exec('start https://github.com/vityakut/proxyapi');
        }
        if (PHP_OS_FAMILY == 'Linux') {
            exec('xdg-open https://github.com/vityakut/proxyapi');
        }
    }

    private function showLinks(): void
    {
        $links = [
            ...self::LINKS,
        ];

        foreach ($links as $message => $link) {
            View::render('components.two-column-detail', [
                'left' => $message,
                'right' => $link,
            ]);
        }
    }
}
