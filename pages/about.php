<?php $title = 'About'; ?>

<section class="text-gray-600 body-font">
    <div class="container mx-auto flex px-5 py-24 md:flex-row flex-col items-center">
        <div class="lg:flex-grow md:w-1/2 lg:pr-24 md:pr-16 flex flex-col md:items-start md:text-left mb-16 md:mb-0 items-center text-center">
            <h1 class="title-font sm:text-4xl text-3xl mb-4 font-medium text-gray-900">A simple PHP app
                <br class="hidden lg:inline-block">built for students
            </h1>
            <p class="mb-8 leading-relaxed">This app demonstrates core PHP concepts including routing, models, migrations, a REST API, and Turbo for SPA-like navigation — all without a framework.</p>
            <div class="flex justify-center">
                <a href="/posts/new" class="inline-flex text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded text-lg">New Post</a>
                <a href="/api/v1/posts" class="ml-4 inline-flex text-gray-700 bg-gray-100 border-0 py-2 px-6 focus:outline-none hover:bg-gray-200 rounded text-lg">API</a>
            </div>
        </div>
        <div class="lg:max-w-lg lg:w-full md:w-1/2 w-5/6">
            <img class="object-cover object-center rounded" alt="about" src="https://dummyimage.com/720x600">
        </div>
    </div>
</section>
