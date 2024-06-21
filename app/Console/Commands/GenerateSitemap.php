<?php


namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $signature = 'leconfe:generate-sitemap';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Generate the sitemap for Leconfe.';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		SitemapGenerator::create(config('app.url'))
			->getSitemap()
			->writeToFile(public_path('sitemap.xml'));
	}
}
