<?php
  require_once __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
  use NeutronStars\Database\Database;

  $database = new Database('database_name', [
      'port'     => 3307,
      'user'    => 'john-doe',
      'password' => 'passwordofjohndoe'
  ]);

  $database->query('articles')
           ->insertInto('title,description,author', ':t1,:d1,:a1', ':t2,:d2,:a2', ':t3,:d3,:a3', ':t4,:d4,:a4')
           ->setParameters([
               ':t1' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
               ':d1' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam in turpis at enim tincidunt blandit. Maecenas suscipit leo at malesuada maximus fusce.',
               ':a1' => 'Lorem ipsum',
               ':t2' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
               ':d2' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam in turpis at enim tincidunt blandit. Maecenas suscipit leo at malesuada maximus fusce.',
               ':a2' => 'Lorem ipsum',
               ':t3' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
               ':d3' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam in turpis at enim tincidunt blandit. Maecenas suscipit leo at malesuada maximus fusce.',
               ':a3' => 'Lorem ipsum',
               ':t4' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
               ':d4' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam in turpis at enim tincidunt blandit. Maecenas suscipit leo at malesuada maximus fusce.',
               ':a4' => 'Lorem ipsum'
           ])
           ->execute();

  $articles = $database->query('articles')
                       ->select('*')
                       ->getResults();

  foreach ($articles as $article) { ?>
      <div class="article">
          <h2><?=$article['title']?></h2>
          <span class="author"><?=$article['author']?></span>
          <p><?=$article['description']?></p>
      </div>
  <?php }

  $database->query('articles')
           ->delete()
           ->execute();
