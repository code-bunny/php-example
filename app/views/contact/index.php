<section class="text-gray-600 body-font relative">
    <div class="absolute inset-0 bg-gray-300">
        <iframe width="100%" height="100%" style="filter: grayscale(1) contrast(1.2) opacity(0.4);" frameborder="0" marginheight="0" marginwidth="0" title="map" scrolling="no"
            src="https://maps.google.com/maps?q=56.2594514,-2.6282968&z=17&iwloc=B&output=embed"></iframe>
    </div>
    <div class="container px-5 py-24 mx-auto flex">
        <turbo-frame id="contact-form" class="lg:w-1/3 md:w-1/2 md:ml-auto w-full mt-10 md:mt-0 relative z-10">
        <div class="bg-white rounded-lg p-8 flex flex-col shadow-md">
            <h2 class="text-gray-900 text-lg mb-1 font-medium title-font">Contact</h2>
            <p class="leading-relaxed mb-5 text-gray-600">The Old Bakehouse, West Green, Crail, Scotland, KY10 3RD</p>

            <?php if ($success): ?>
                <div class="mb-4 bg-green-50 border border-green-200 rounded p-3 text-sm text-green-700">
                    Message sent — we'll be in touch.
                </div>
            <?php endif; ?>

            <?php if ($errors): ?>
                <div class="mb-4 bg-red-50 border border-red-200 rounded p-3 text-sm text-red-700 space-y-1">
                    <?php foreach ($errors as $error): ?>
                        <p><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/contact">
                <?php csrf_field() ?>
                <div class="relative mb-4">
                    <label for="email" class="leading-7 text-sm text-gray-600">Email</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>"
                        class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                </div>
                <div class="relative mb-4">
                    <label for="message" class="leading-7 text-sm text-gray-600">Message</label>
                    <textarea id="message" name="message" class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 h-32 text-base outline-none text-gray-700 py-1 px-3 resize-none leading-6 transition-colors duration-200 ease-in-out"><?= htmlspecialchars($message) ?></textarea>
                </div>
                <button type="submit" class="text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded text-lg">Send</button>
            </form>
        </div>
        </turbo-frame>
    </div>
</section>
