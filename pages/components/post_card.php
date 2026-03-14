<?php

function post_card(Post $post): void { ?>
    <article class="bg-white border border-gray-200 rounded-lg p-5">
        <p class="text-xs text-gray-400 font-mono mt-3"><?= $post->id ?></p>
        <h2 class="text-lg font-semibold mb-1"><?= htmlspecialchars($post->title) ?></h2>
        <div class="text-gray-700 text-sm"><?= $post->body ?></div>
    </article>
<?php }
