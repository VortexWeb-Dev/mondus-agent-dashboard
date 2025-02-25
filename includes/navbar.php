<!-- <nav class="bg-gray-800 text-white p-4">
    <div class="container mx-auto flex justify-between items-center">

        <div class="text-2xl font-bold">
            <a href="index.php" class="hover:text-gray-300">MyApp</a>
        </div>

        <div class="hidden md:flex space-x-4">
            <a href="dashboard.php" class="hover:text-gray-300 <?php echo $current_page === 'dashboard.php' ? 'text-gray-300' : ''; ?>">Dashboard</a>
            <a href="users.php" class="hover:text-gray-300 <?php echo $current_page === 'users.php' ? 'text-gray-300' : ''; ?>">Users</a>
            <a href="products.php" class="hover:text-gray-300 <?php echo $current_page === 'products.php' ? 'text-gray-300' : ''; ?>">Products</a>
            <a href="settings.php" class="hover:text-gray-300 <?php echo $current_page === 'settings.php' ? 'text-gray-300' : ''; ?>">Settings</a>
        </div>

        <div class="flex items-center space-x-2">
            <div class="text-sm md:block hidden">Hello, User</div>
            <button class="bg-gray-700 p-2 rounded-full hover:bg-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A1 1 0 016.293 17h11.414a1 1 0 01.707.293l2.586 2.586a1 1 0 001.414 0l2.293-2.293a1 1 0 00-1.414-1.414l-2.586 2.586a1 1 0 01-.707.293H6.293a1 1 0 01-.707-.293L3 15.707a1 1 0 010-1.414l2.121-2.121a1 1 0 01.707-.293h11.414a1 1 0 01.707.293L21 14.707a1 1 0 010 1.414l-2.121 2.121a1 1 0 01-.707.293H6.293a1 1 0 01-.707-.293L3 15.707a1 1 0 010-1.414l2.121-2.121a1 1 0 01.707-.293h11.414a1 1 0 01.707.293L21 14.707a1 1 0 010 1.414l-2.121 2.121a1 1 0 01-.707.293H6.293a1 1 0 01-.707-.293L3 15.707a1 1 0 010-1.414l2.121-2.121a1 1 0 01.707-.293h11.414a1 1 0 01.707.293L21 14.707a1 1 0 010 1.414l-2.121 2.121a1 1 0 01-.707.293H6.293a1 1 0 01-.707-.293L3 15.707a1 1 0 010-1.414l2.121-2.121a1 1 0 01.707-.293h11.414a1 1 0 01.707.293L21 14.707a1 1 0 010 1.414l-2.121 2.121a1 1 0 01-.707.293H6.293a1 1 0 01-.707-.293L3 15.707a1 1 0 010-1.414l2.121-2.121a1 1 0 01.707-.293h11.414a1 1 0 01.707.293L21 14.707a1 1 0 010 1.414l-2.121 2.121a1 1 0 01-.707.293H6.293a1 1 0 01-.707-.293L3 15.707a1 1 0 010-1.414l2.121-2.121a1 1 0 01.707-.293h11.414a1 1 0 01.707.293L21 14.707a1 1 0 010 1.414l-2.121 2.121a1 1 0 01-.707.293H6.293a1 1 0 01-.707-.293L3 15.707a1 1 0 010-1.414l2.121-2.121a1 1 0 01.707-.293h11.414a1 1 0 01.707.293L21 14.707a1 1 0 010 1.414l-2.121 2.121a1 1 0 01-.707.293H6.293a1 1 0 01-.707-.293L3 15.707a1 1 0 010-1.414l2.121-2.121a1 1 0 01.707-.293h11.414a1 1 0 01.707.293L21 14.707a1 1 0 010 1.414l-2.121 2.121a1 1 0 01-.707.293H6.293a1 1 0 01-.707-.293L3 15.707a1 1 0 010-1.414l2.121-2.121a1 1 0 01.707-.293h11.414a1 1 0 01.707.293L21 14.707a1 1 0 010 1.414l-2.121 2.121a1 1 0 01-.707.293H6.293a1 1 0 01-.707-.293L3 15.707a1 1 0 010-1.414l2.121-2.121a1 1 0 01.707-.293h11.414a1 1 0 01.707.293L21 14.707a1 1 0 010 1.414l-2.121 2.121a1 1 0 01-.707.293H6.293a1 1 0 01-.707-.293L3 15.707a1 1 0 010-1.414l2.121-2.121a1 1 0 01.707-.293h11.414a1 1 0 01.707.293L21 14.707a1 1 0 010 1.414l-2.121 2.121a1 1 0 01-.707.293H6.293a1 1 0 01-.707-.293L3 15.707a1 1 0 010-1.414l2.121-2.121a1 1 0 01.707-.293h11.414a1 1 0 01.707.293L21 14.707a1 1 0 010 1.414l-2.121 2.121a1 1 0 01-.707.293H6.293a1 1 0 01-.707-.293L3 15.707a1 1 0 010-1.414l2.121-2.121a1 1 0 01.707-.293h11.414a1 1 0 01.707.293L21 14.707a1 1 0 010 1.414l-2.121 2.121a1 1 0 01-.707.293H6.293a1 1 0 01-.707-.293L3 15.707a1 1 0 010-1.414l2.121-2.121a1 1 0 01.707-.293h11.414a1 1 0 01.707.293L21 14.707a1 1 0 010 1.414l-2.121 2.121a1 1 0 01-.707.293H6.293a1 1 0 01-.707-.293L3 15.707a1 1 0 010-1.414l2.121-2.121a1 1 0 01.707-.293h11.414a1 1 0 01.707.293L21 14.707a1 1 0 010 1.414l-2.121 2.121a1 1 0 01-.707.293H6.293a1 1 0 01-.707-.293L3 15.707a1 1 0 010-1.414l2.121-2.121a1 1 0 01.707-.293h11.414a1 1 0 01.707.293L21 14.707a1 1 0 010 1.414l-2.121 2.121a1 1 0 01-.707.293H6.293a1 1 0 01-.707-.293L3 15.707a1 1 0 010-1.414l2.121-2.121a1 1 0 01.707-.293h11.414a1 1 0 01.707.293L21 14.707a1 1 0 010 1.414l-2.121 2.121a1 1 0 01-.707.293H6.293a1 1 0 01-.707-.293L3 15.707a1 1 0 010-1.414l2.121-2.121a1 1 0 01.707-.293h11.414a1 1 0 01.707.293L21 14.707a1 1 0 010 1.414l-2.121 2.121a1 1 0 01-.707.293H6.293a1 1 0 01-.707-.293L3 15.707a1 1 0 010-1.414l2.121-2.121a1 1 0 01.707-.293h11.414a1 1 0 01.707.293L21 14.707a1 1 0 010 1.414l-2.121 2.121a1 1 0 01-.707.293H6.293a1 1 0 01-.707-.293L3 15.707a1 1 0 010-1.414l2.121-2.121a1 1 0 01.707-.293h11.414a1 1 0 01.707.293L21 14.707a1 1 0 010 1.414l-2.121 2.121a1 1 0 01-.707.293H6.293a1 1 0 01-.707-.293L3 15.707a1 1 0 010-1.414l2.121-2.121a1 1 0 01.707-.293h11.414a1 1 0 01.707.293L21 14.707a1 1 0 010 1.414l-2.121 2.121a1 1 0 01-.707.293H6.293a1 1 0 01-.707-.293L3 15.707a1 1 0 010-1.414l2.121-2.121a1 1 0 01.707-.293h11.414a1 1 0 01.707.293L21 14.707a1 1 0 010 1.414l-2.121 2.121a1 1 0 01-.707.293H6.293a1 1 0 01-.707-.293L3 15.707a1 1 0 010-1.414l2.121-2.121a1 1 0 01.707-.293h11.414a1 1 0 01.707.293L21 14.707a1 1 0 010 1.414l-2.121 2.121a1 1 0 01-.707.293H6.293a1 1 0 01-.707-.293L3 15.707a1 1 0 010-1.414z" />
                </svg>
            </button>
        </div>

    </div>
</nav> -->

<div class="px-8 py-5 flex justify-between bg-white shadow-md dark:bg-gray-800">
    <h2 class="text-3xl font-bold text-gray-900 dark:text-white"></h2>
    <!-- <div class="flex items-center">
        <a href="overall_deals.php" class="px-4 py-2 mr-4 bg-gray-300 rounded-md dark:bg-gray-700">View Overall Deals</a>
    </div> -->
    <!-- toggle -->
    <!-- <button id="themeToggle" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2 px-4 rounded-full shadow-lg">
        Toggle Theme
    </button> -->
    <button
        id="darkModeToggle"
        class="p-2 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200"
        aria-label="Toggle dark mode">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
        </svg>
    </button>
</div>

<script>
    const isDarkMode = localStorage.getItem('darkMode') === 'true';
    document.documentElement.classList.toggle('dark', isDarkMode);
    document.getElementById('darkModeToggle').innerHTML = isDarkMode ?
        '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>' :
        '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>';
    document.getElementById('darkModeToggle').addEventListener('click', () => {
        const isDarkMode = !document.documentElement.classList.contains('dark');
        localStorage.setItem('darkMode', isDarkMode.toString());
        document.documentElement.classList.toggle('dark', isDarkMode);
        document.getElementById('darkModeToggle').innerHTML = isDarkMode ?
            '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>' :
            '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>';
    });
</script>
