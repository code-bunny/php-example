<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'My App') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script type="module">
        import * as Turbo from 'https://esm.sh/@hotwired/turbo';
    </script>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen flex flex-col">

    <header class="text-gray-600 body-font">
        <div class="container mx-auto flex flex-wrap p-5 flex-col md:flex-row items-center">
            <a href="/" class="flex title-font font-medium items-center text-gray-900 mb-4 md:mb-0">
                <svg viewBox="0 0 105 105" fill="currentColor" class="text-indigo-500 h-10 w-10 duration-300 transform hover:text-indigo-400 hover:scale-110">
                    <path style="fill:none" d="M0 0h266.667v266.667H0z" transform="matrix(.39075 0 0 .39058 0 .001)"></path>
                    <path d="M261.045 162.704c12.144-25.597 6.867-83.363-38.347-90.502-25.099-3.962-52.337 11.082-65.309 19.588-9.643-16.259-32.786-53.532-53.499-73.265C89.359 4.538 70.958 1.421 57.22 10.561c-11.017 7.356-20.277 25.333-10.066 51.841 9.102 23.669 36.155 44.511 64.794 66.582 22.456 17.342 45.771 35.265 58.783 53.954 25.917 37.22 3.593 66.225 2.642 67.361a8.215 8.215 0 0 0-1.827 5.164c0 4.507 3.709 7.445 8.217 7.445h47.769c4.508 0 11.611.265 11.611-4.242 0-4.506-2.311-11.051-16.403-11.051h-28.919c1.724-4.021 3.082-8.557 4.056-12.822a70.559 70.559 0 0 0 .938-26.218c26.749-3.962 50.79-21.757 62.23-45.871ZM207.85 131.85h-.013c-6.087 0-11.096-5.009-11.096-11.095s5.009-11.095 11.096-11.095 11.096 5.009 11.096 11.095-5.009 11.095-11.096 11.095h-.04.053Z" style="fill-rule:nonzero" transform="matrix(.39075 0 0 .39058 0 .001)"></path>
                    <path d="M139.411 247.615c13.484-.288 14.887 8.659 14.903 11.396.015 2.736-2.771 3.855-7.242 3.897 0 0-34.998-.125-78.799-.706-19.927-.264-30.616-9.474-41.199-19.681-9.924 1.618-23.927-2.433-26.612-12.828-1.528-5.916.174-16.747 13.746-22.496.375-44.497 31.522-66.01 67.334-66.898-33.242-17.891-58.536-46.5-67.154-72.244C9.439 53.273 7.247 38.162 13.2 21.804 19.577 4.281 39.56 1.293 42.742 5.375c2.188 2.806-16.01 6.318-19.336 24.989-2.828 15.877 2.74 30.397 10.378 44.388 11.466 21.001 43.24 46.092 59.678 56.737 12.935 8.376 30.579 26.394 19.996 24.683-6.998-1.131-15.467-1.475-26.48-1.215-10.378.245-52.198 4.683-56.907 44.61-1.439 12.203 4.175 30.55 17.701 40.052 10.593 7.442 24.589 7.791 35.483 7.996.366.007 42.401.294 56.156 0Z" style="fill-rule:nonzero" transform="matrix(.39075 0 0 .39058 0 .001)"></path>
                    <path d="M28.115 76.284a.467.467 0 0 0 0 .66l3.73 3.728a2.41 2.41 0 0 1 0 3.404l-.061.06c-.94.94-2.465.94-3.405 0l-6.538-6.534a1.395 1.395 0 0 1 0-1.975l6.538-6.534c.94-.94 2.464-.94 3.405 0l.06.06a2.407 2.407 0 0 1 0 3.404l-3.729 3.727Z"></path>
                </svg>
                <span class="ml-3 text-xl">My App</span>
            </a>
            <nav class="md:mr-auto md:ml-4 md:py-1 md:pl-4 md:border-l md:border-gray-400 flex flex-wrap items-center text-base justify-center">
                <a href="/" class="mr-5 hover:text-gray-900">Home</a>
                <a href="/about" class="mr-5 hover:text-gray-900">About</a>
                <a href="/contact" class="mr-5 hover:text-gray-900">Contact</a>
            </nav>
            <a href="/posts/new" class="inline-flex items-center bg-gray-100 border-0 py-1 px-3 focus:outline-none hover:bg-gray-200 rounded text-base mt-4 md:mt-0">New Post
                <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-4 h-4 ml-1" viewBox="0 0 24 24">
                    <path d="M5 12h14M12 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    </header>

    <?php if (isset($_GET['subscribed'])): ?>
    <div class="bg-indigo-500 text-white text-sm text-center py-2 px-4">
        You're subscribed — thanks!
    </div>
    <?php endif; ?>

    <main class="container mx-auto px-5 py-8 flex-grow">
        <?= $content ?>
    </main>

    <footer class="text-gray-600 body-font">
        <div class="container px-5 py-24 mx-auto">
            <div class="flex flex-wrap md:text-left text-center order-first">
                <div class="lg:w-1/4 md:w-1/2 w-full px-4">
                    <h2 class="title-font font-medium text-gray-900 tracking-widest text-sm mb-3">PAGES</h2>
                    <nav class="list-none mb-10">
                        <li><a href="/" class="text-gray-600 hover:text-gray-800">Home</a></li>
                        <li><a href="/about" class="text-gray-600 hover:text-gray-800">About</a></li>
                        <li><a href="/contact" class="text-gray-600 hover:text-gray-800">Contact</a></li>
                        <li><a href="/posts/new" class="text-gray-600 hover:text-gray-800">New Post</a></li>
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
                    <svg viewBox="0 0 105 105" fill="currentColor" class="text-indigo-500 h-10 w-10 duration-300 transform hover:text-indigo-400 hover:scale-110">
                        <path style="fill:none" d="M0 0h266.667v266.667H0z" transform="matrix(.39075 0 0 .39058 0 .001)"></path>
                        <path d="M261.045 162.704c12.144-25.597 6.867-83.363-38.347-90.502-25.099-3.962-52.337 11.082-65.309 19.588-9.643-16.259-32.786-53.532-53.499-73.265C89.359 4.538 70.958 1.421 57.22 10.561c-11.017 7.356-20.277 25.333-10.066 51.841 9.102 23.669 36.155 44.511 64.794 66.582 22.456 17.342 45.771 35.265 58.783 53.954 25.917 37.22 3.593 66.225 2.642 67.361a8.215 8.215 0 0 0-1.827 5.164c0 4.507 3.709 7.445 8.217 7.445h47.769c4.508 0 11.611.265 11.611-4.242 0-4.506-2.311-11.051-16.403-11.051h-28.919c1.724-4.021 3.082-8.557 4.056-12.822a70.559 70.559 0 0 0 .938-26.218c26.749-3.962 50.79-21.757 62.23-45.871ZM207.85 131.85h-.013c-6.087 0-11.096-5.009-11.096-11.095s5.009-11.095 11.096-11.095 11.096 5.009 11.096 11.095-5.009 11.095-11.096 11.095h-.04.053Z" style="fill-rule:nonzero" transform="matrix(.39075 0 0 .39058 0 .001)"></path>
                        <path d="M139.411 247.615c13.484-.288 14.887 8.659 14.903 11.396.015 2.736-2.771 3.855-7.242 3.897 0 0-34.998-.125-78.799-.706-19.927-.264-30.616-9.474-41.199-19.681-9.924 1.618-23.927-2.433-26.612-12.828-1.528-5.916.174-16.747 13.746-22.496.375-44.497 31.522-66.01 67.334-66.898-33.242-17.891-58.536-46.5-67.154-72.244C9.439 53.273 7.247 38.162 13.2 21.804 19.577 4.281 39.56 1.293 42.742 5.375c2.188 2.806-16.01 6.318-19.336 24.989-2.828 15.877 2.74 30.397 10.378 44.388 11.466 21.001 43.24 46.092 59.678 56.737 12.935 8.376 30.579 26.394 19.996 24.683-6.998-1.131-15.467-1.475-26.48-1.215-10.378.245-52.198 4.683-56.907 44.61-1.439 12.203 4.175 30.55 17.701 40.052 10.593 7.442 24.589 7.791 35.483 7.996.366.007 42.401.294 56.156 0Z" style="fill-rule:nonzero" transform="matrix(.39075 0 0 .39058 0 .001)"></path>
                        <path d="M28.115 76.284a.467.467 0 0 0 0 .66l3.73 3.728a2.41 2.41 0 0 1 0 3.404l-.061.06c-.94.94-2.465.94-3.405 0l-6.538-6.534a1.395 1.395 0 0 1 0-1.975l6.538-6.534c.94-.94 2.464-.94 3.405 0l.06.06a2.407 2.407 0 0 1 0 3.404l-3.729 3.727Z"></path>
                    </svg>
                    <span class="ml-3 text-xl">My App</span>
                </a>
                <p class="text-sm text-gray-500 sm:ml-6 sm:mt-0 mt-4">© <?= date('Y') ?> My App</p>
                <span class="inline-flex sm:ml-auto sm:mt-0 mt-4 justify-center sm:justify-start">
                    <a class="text-gray-500 cursor-pointer">
                        <svg fill="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
                            <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"></path>
                        </svg>
                    </a>
                    <a class="ml-3 text-gray-500 cursor-pointer">
                        <svg fill="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
                            <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"></path>
                        </svg>
                    </a>
                    <a class="ml-3 text-gray-500 cursor-pointer">
                        <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
                            <rect width="20" height="20" x="2" y="2" rx="5" ry="5"></rect>
                            <path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37zm1.5-4.87h.01"></path>
                        </svg>
                    </a>
                    <a class="ml-3 text-gray-500 cursor-pointer">
                        <svg fill="currentColor" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="0" class="w-5 h-5" viewBox="0 0 24 24">
                            <path stroke="none" d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"></path>
                            <circle cx="4" cy="4" r="2" stroke="none"></circle>
                        </svg>
                    </a>
                </span>
            </div>
        </div>
    </footer>

</body>
</html>
