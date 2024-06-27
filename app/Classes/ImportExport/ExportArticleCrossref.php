<?php

namespace App\Classes\ImportExport;

use App\Models\Submission;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Spatie\ArrayToXml\ArrayToXml;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\Concerns\AsAction;

class ExportArticleCrossref
{
	protected string $testApi = 'https://test.crossref.org/servlet/deposit';

	protected string $productionApi = 'https://doi.crossref.org/servlet/deposit';

	public string $batchId;

	public ArrayToXml $arrayData;

	public function __construct(public Submission $submission)
	{
		$this->batchId   = Str::random();
		$this->arrayData = $this->createArrayData();

		$this->getLog();
	}

	public function getXml()
	{
		// Save file
		// Storage::disk('local')->put('crossref.xml', $this->arrayData->toXml());

		return $this->arrayData->toXml();
	}

	public function depositXml()
	{
		$url = $this->testApi;

		$xml = $this->getXml();

		return Http::attach('fname', $xml, 'crossref.xml')
			->post($url, [
				'operation' => 'doMDUpload',
				'login_id' => 'alma@openjournaltheme.com/testojt',
				'login_passwd' => 'devojt1995',
			]);
	}



	public function createArrayData(): ArrayToXml
	{
		$metadata = [];

		$metadata['head'] = $this->createHead();
		$metadata['body'] = $this->createBody();

		$arrayToXml = new ArrayToXml(
			$metadata,
			[
				'rootElementName' => 'doi_batch',
				'_attributes' => [
					'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
					'xsi:schemaLocation' => 'http://www.crossref.org/schema/5.3.0 https://www.crossref.org/schemas/crossref5.3.0.xsd',
					'xmlns' => 'http://www.crossref.org/schema/5.3.0',
					'xmlns:jats' => 'http://www.ncbi.nlm.nih.gov/JATS1',
					'xmlns:fr' => 'http://www.crossref.org/fundref.xsd',
					'xmlns:mml' => 'http://www.w3.org/1998/Math/MathML',
					'version' => '5.3.0',
				],
			],
			true,
			'UTF-8',
			'1.0',
			[],
		);

		return $arrayToXml->prettify();
	}

	protected function createHead()
	{
		return [
			'doi_batch_id' => $this->batchId,
			'timestamp' => date('YmdHis') . '00',
			'depositor' => [
				'depositor_name' => 'Rahman Ramsi', //TODO Add input
				'email_address' => 'rahmanramsi19@gmail.com', //TODO Add input
			],
			'registrant' => 'Open Journal Theme', //TODO Add input 
		];
	}

	protected function createBody()
	{
		$body = [
			'conference' => [
				'event_metadata' => $this->createEventMetadata(),
				'proceedings_series_metadata' => $this->createProceedingsSeriesMetadata(),
				'conference_paper' => $this->createConferencePaper(),
			]
		];

		return $body;
	}

	protected function createEventMetadata(): array
	{
		return [
			'conference_name' => $this->submission->conference->name,
			'conference_theme' => $this->submission->conference->getMeta('theme'),
			'conference_acronym' => $this->submission->conference->getMeta('acronym'),
			'conference_number' => $this->submission->conference->getMeta('number'),
			'conference_location' => $this->submission->conference->getMeta('location'),
			'conference_date' => [
				'_attributes' => [
					'start_day' => "12", //TODO fix this
					'start_month' => "11", //TODO fix this
					'start_year' => "2018",
					'end_day' => "14",
					'end_month' => "11",
					'end_year' => "2018",
				],
				'_value' => "Nov. 13-14, 2018",
			]
		];
	}

	protected function createProceedingsSeriesMetadata() : array 
	{
		return [
			'series_metadata' => $this->createSeriesMetadata(),
			'proceedings_title' => 'Proceedings Title',
			'volume' => $this->submission->proceeding->volume,
			'proceedings_subject' => 'Proceedings Subject', //TODO Add input
			'publisher' => [
				'publisher_name' => 'Open Journal Theme', //TODO Add input
				'publisher_place' => 'Indonesia', //TODO Add input
			],
			'publication_date' => [
				'_attributes' => [
					'media_type' => 'online',
				],
				'month' => '11',
				'day' => '13',
				'year' => '2018',
			],
			'noisbn' => [
				'_attributes' => [
					'reason' => 'simple_series'
				]
			],
		];
	}

	protected function createSeriesMetadata() : array 
	{
		return [
			'contributors' => [
				'person_name' => [
					[
						'_attributes' => [
							'sequence' => 'first',
							'contributor_role' => 'chair',
						],
						'given_name' => 'Rahman',
						'surname' => 'Ramsi',
					],

				]
			],
			'titles' => [
				'title' => 'Series Title',
			],
		];
	}

	protected function createConferencePaper() : array 
	{
		return [
			'contributors' => [
				'person_name' => [
					[
						'_attributes' => [
							'sequence' => 'first',
							'contributor_role' => 'author',
						],
						'given_name' => 'Alma',
						'surname' => 'Dani',
					],

				]
			],
			'titles' => [
				'title' => 'Paper Title',
			],
			'jats:abstract'  => [
				'jats:p' => strip_tags($this->submission->getMeta('abstract'))
			],
			'publication_date' => [
				'_attributes' => [
					'media_type' => 'online',
				],
				'month' => '11',
				'day' => '13',
				'year' => '2018',
			],
			'pages' => [
				'first_page' => '1',
				'last_page' => '10',
			],
			'doi_data' => [ // DOI data tidak wajib
				'doi' => $this->submission->doi->doi,
				'resource' => route('livewirePageGroup.conference.pages.submission-detail', ['submission' => $this->submission]),
				'collection' => []
			],
		];
	}

	public function getLog()
	{
		// https://test.crossref.org/servlet/submissionDownload?usr=_role_&pwd=_password_&doi_batch_id=_doi batch id_&file_name=filename&type=_submission type_
		$response = Http::get('https://test.crossref.org/servlet/submissionDownload', [
			'usr' => 'alma@openjournaltheme.com/testojt',
			'pwd' => 'devojt1995',
			// 'doi_batch_id' => 'QM42dfgRqPL6JDWn',
			'file_name' => 'crossref.xml',
			'type' => 'result'
		]);
		dd($response->getBody()->getContents());
	}
}
