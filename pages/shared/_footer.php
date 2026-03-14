<?php
require_once __DIR__ . '/../components/social_icon.php';
$social = require __DIR__ . '/../../config/social.php';
?>
<footer class="text-gray-600 body-font">
    <div class="container px-5 py-24 mx-auto">
        <div class="flex flex-wrap md:text-left text-center order-first">
            <div class="lg:w-1/4 md:w-1/2 w-full px-4">
                <h2 class="title-font font-medium text-gray-900 tracking-widest text-sm mb-3">PAGES</h2>
                <nav class="list-none mb-10">
                    <li><a href="/" class="text-gray-600 hover:text-gray-800">Home</a></li>
                    <li><a href="/blog" class="text-gray-600 hover:text-gray-800">Blog</a></li>
                    <li><a href="/about" class="text-gray-600 hover:text-gray-800">About</a></li>
                    <li><a href="/contact" class="text-gray-600 hover:text-gray-800">Contact</a></li>
                </nav>
            </div>
            <div class="lg:w-1/4 md:w-1/2 w-full px-4">
                <h2 class="title-font font-medium text-gray-900 tracking-widest text-sm mb-3">TOOLS</h2>
                <nav class="list-none mb-10">
                    <li><a href="http://localhost:8081" class="text-gray-600 hover:text-gray-800">Swagger UI</a></li>
                    <li><a href="http://localhost:8080" class="text-gray-600 hover:text-gray-800">phpMyAdmin</a></li>
                </nav>
            </div>
            <div class="lg:w-1/2 md:w-full w-full px-4">
                <h2 class="title-font font-medium text-gray-900 tracking-widest text-sm mb-3">SUBSCRIBE</h2>
                <div class="flex xl:flex-nowrap md:flex-nowrap lg:flex-wrap flex-wrap justify-center items-end md:justify-start">
                    <form method="POST" action="/subscribe" class="flex xl:flex-nowrap md:flex-nowrap lg:flex-wrap flex-wrap items-end gap-2">
                        <?php csrf_field() ?>
                        <div class="relative w-40 sm:w-auto">
                            <label for="footer-field" class="leading-7 text-sm text-gray-600">Email address</label>
                            <input type="email" id="footer-field" name="email" required class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:bg-transparent focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                        </div>
                        <button type="submit" class="lg:mt-2 xl:mt-0 flex-shrink-0 inline-flex text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded">Subscribe</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="bg-gray-100">
        <div class="container px-5 py-6 mx-auto flex items-center sm:flex-row flex-col">
            <a href="/" class="flex title-font font-medium items-center md:justify-start justify-center text-gray-900">
                <?php logo() ?>
                <span class="ml-2 text-xl">My App</span>
            </a>
            <p class="text-sm text-gray-500 sm:ml-6 sm:mt-0 mt-4">© <?= date('Y') ?> My App</p>
            <span class="inline-flex sm:ml-auto sm:mt-0 mt-4 justify-center sm:justify-start">
                <?php foreach ($social as $i => $link): ?>
                    <?php if (empty($link['url'])) continue ?>
                    <a href="<?= htmlspecialchars($link['url']) ?>"
                       aria-label="<?= htmlspecialchars($link['label']) ?>"
                       class="<?= $i > 0 ? 'ml-3 ' : '' ?>text-gray-500 hover:text-gray-800">
                        <?php social_icon($link['icon']) ?>
                    </a>
                <?php endforeach ?>
            </span>
        </div>
    </div>
</footer>
