<?php

function post_card(Post $post): void { ?>
    <div class="p-12 md:w-1/2 flex flex-col items-start">
        <span class="inline-block py-1 px-2 rounded bg-indigo-50 text-indigo-500 text-xs font-mono tracking-widest">Posts</span>
        <h2 class="sm:text-3xl text-2xl title-font font-medium text-gray-900 mt-4 mb-4"><?= htmlspecialchars($post->title) ?></h2>
        <p class="leading-relaxed mb-8"><?= htmlspecialchars($post->body) ?></p>
        <div class="flex items-center flex-wrap mt-auto w-full">
            <a href="/posts/<?= $post->id ?>" class="text-indigo-500 inline-flex items-center">Read More
                <svg class="w-4 h-4 ml-2" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14"></path>
                    <path d="M12 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    </div>
<?php }
