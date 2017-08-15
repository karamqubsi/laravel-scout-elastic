<?php

namespace ScoutEngines\Elasticsearch;

use Laravel\Scout\EngineManager;
use Illuminate\Support\ServiceProvider;
use Elasticsearch\ClientBuilder as ElasticBuilder;

class ElasticsearchProvider extends ServiceProvider
{
  /**
  * Bootstrap the application services.
  */
  protected $client;

  public function boot()
  {
    $this->client = ElasticBuilder::create()
    ->setHosts(config('scout.elasticsearch.hosts'))
    ->build();
    /* check for index */
    $indexpara['index'] = config('scout.elasticsearch.index');
    $isIndexCreated = $this->client->indices()->exists($indexpara);
    #if it wasn't created , create it with this sittings.
    if (!$isIndexCreated) {
      $params = [
        'index' => config('scout.elasticsearch.index'),
        'body' => [
          'settings' => [
            'number_of_shards' => 3,
            'number_of_replicas' => 2
          ]
        ]
      ];
      // Create the index with mappings and settings now
      $response = $this->client->indices()->create($params);

    }



    app(EngineManager::class)->extend('elasticsearch', function($app) {
      return new ElasticsearchEngine($this->client,
      config('scout.elasticsearch.index')
    );
  });
}
}
