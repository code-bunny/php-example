<?php

require_once __DIR__ . '/components/post_card.php';

require_once __DIR__ . '/../db.php';
Model::setDb($pdo);

$posts = Post::paginate(4, 0);
$title = 'Home';
?>

<section class="text-gray-600 body-font">
  <div class="container px-5 py-24 mx-auto flex flex-wrap">
    <div class="flex w-full mb-20 flex-wrap">
      <h1 class="sm:text-3xl text-2xl font-medium title-font text-gray-900 lg:w-1/3 lg:mb-0 mb-4">Master Cleanse Reliac Heirloom</h1>
      <p class="lg:pl-6 lg:w-2/3 mx-auto leading-relaxed text-base">Whatever cardigan tote bag tumblr hexagon brooklyn asymmetrical gentrify, subway tile poke farm-to-table. Franzen you probably haven't heard of them man bun deep jianbing selfies heirloom.</p>
    </div>
    <div class="flex flex-wrap md:-m-2 -m-1">
      <div class="flex flex-wrap w-1/2">
        <div class="md:p-2 p-1 w-1/2">
          <img alt="gallery" class="w-full object-cover h-full object-center block" src="https://placecats.com/500/300">
        </div>
        <div class="md:p-2 p-1 w-1/2">
          <img alt="gallery" class="w-full object-cover h-full object-center block" src="https://placecats.com/501/301">
        </div>
        <div class="md:p-2 p-1 w-full">
          <img alt="gallery" class="w-full h-full object-cover object-center block" src="https://placecats.com/600/360">
        </div>
      </div>
      <div class="flex flex-wrap w-1/2">
        <div class="md:p-2 p-1 w-full">
          <img alt="gallery" class="w-full h-full object-cover object-center block" src="https://placecats.com/601/361">
        </div>
        <div class="md:p-2 p-1 w-1/2">
          <img alt="gallery" class="w-full object-cover h-full object-center block" src="https://placecats.com/502/302">
        </div>
        <div class="md:p-2 p-1 w-1/2">
          <img alt="gallery" class="w-full object-cover h-full object-center block" src="https://placecats.com/503/303">
        </div>
      </div>
    </div>
  </div>
</section>

<?php if (empty($posts)): ?>
    <p class="text-gray-500">No posts yet. <a href="/posts/new" class="text-blue-600 hover:underline">Create one.</a></p>
<?php else: ?>
    <section class="text-gray-600 body-font overflow-hidden">
        <div class="flex flex-wrap -m-12">
            <?php foreach ($posts as $post): ?>
                <?php post_card($post) ?>
            <?php endforeach; ?>
        </div>
    </section>
    <div class="text-center mt-12">
        <a href="/blog" class="inline-flex items-center text-indigo-500 hover:text-indigo-700">View all posts
            <svg class="w-4 h-4 ml-2" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14"></path>
                <path d="M12 5l7 7-7 7"></path>
            </svg>
        </a>
    </div>
<?php endif; ?>
