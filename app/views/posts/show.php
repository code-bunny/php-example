<section class="text-gray-600 body-font overflow-hidden">
    <div class="px-5 py-12">
        <div class="flex flex-col items-start">
            <span class="inline-block py-1 px-2 rounded bg-indigo-50 text-indigo-500 text-xs font-mono tracking-widest">Posts</span>
            <h1 class="sm:text-3xl text-2xl title-font font-medium text-gray-900 mt-4 mb-4"><?= htmlspecialchars($post->title) ?></h1>
            <p class="leading-relaxed mb-8"><?= htmlspecialchars($post->body) ?></p>
            <div class="flex items-center flex-wrap mt-auto w-full pb-4 border-b-2 border-gray-100">
                <a href="/" class="text-indigo-500 inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 12H5"></path>
                        <path d="M12 19l-7-7 7-7"></path>
                    </svg>
                    Back
                </a>
            </div>
        </div>
    </div>
</section>
