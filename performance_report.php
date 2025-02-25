<?php include('includes/header.php'); ?>
<?php include('includes/sidebar.php'); ?>

<div class="w-[85%] bg-gray-100 dark:bg-gray-900 relative">
    <?php include('includes/navbar.php'); ?>

    <!-- Loading Spinner -->
    <div id="loadingSpinner" class="absolute inset-0 flex items-center justify-center">
        <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-blue-500"></div>
    </div>

    <div class="max-w-5xl mx-auto px-6 py-8 pt-0" id="formContainer" style="display: none;">
        <h1 class="text-3xl font-bold text-center text-gray-800 dark:text-white">
            <span class="month"></span> <span class="year"></span> Performance Evaluation
        </h1>
        <p class="text-lg font-semibold text-center text-gray-600 dark:text-gray-400 mb-8 "><span class="username"></span></p>

        <!-- Form Begins -->
        <form class="space-y-8 bg-white dark:bg-gray-800 rounded-lg p-8 shadow-md">
            <!-- Input Groups -->
            <?php
            $fields = [
                'No. of Marketing Leads Received (Including repeat inquiries)' => 'marketingLeads',
                'Active Leads' => 'activeLeads',
                'Unqualified/Reassigned Leads' => 'unqualifiedLeads',
                'Leads without Update for more than 2 weeks' => 'leadsWithoutUpdate',
                'New Deals Created in Hubspot' => 'newHubspotDeals',
                'No. of Meetings Conducted in Hubspot' => 'newHubspotMeetings',
                'No. Live Ads' => 'propertyFinderAds',
                'No. of Leads Received from PropertyFinder Ads' => 'propertyFinderLeads',
                'Total Earnings Year to Date' => 'totalEarnings',
                'Total Worth of Properties Sold Year to Date' => 'totalWorthOfProperties',
                'Average Monthly Earnings' => 'avgMonthlyEarnings',
                'Rank for Month' => 'ranking',
                'YTD Ranking' => 'ytdRanking',
            ];

            foreach ($fields as $label => $name) {
                echo "<div>
                        <label class='block font-semibold mb-2 text-gray-700 dark:text-gray-300'>$label</label>
                        <input disabled type='text' class='form-input dark:bg-gray-700 dark:text-gray-100 w-full' name='$name'>
                    </div>";
            }
            ?>

            <!-- Deals Table -->
            <div>
                <h3 class="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-100">Deals in <span class="year"></span></h3>
                <table class="w-full border-collapse bg-white dark:bg-gray-700 shadow-md rounded-lg overflow-hidden">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-300">
                            <th class="p-4 text-left">Unit Details</th>
                            <th class="p-4 text-left">Property Price</th>
                            <th class="p-4 text-left">Type</th>
                            <th class="p-4 text-left">Net Commission</th>
                            <th class="p-4 text-left">Date</th>
                        </tr>
                    </thead>
                    <tbody id="dealsTable">

                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>

<script>
    function formatDate(dateString) {
        const date = new Date(dateString);
        const day = date.getDate();
        const month = date.toLocaleString('default', {
            month: 'long'
        });
        const year = date.getFullYear();
        return `${day} ${month} ${year}`;
    }

    document.addEventListener("DOMContentLoaded", function() {
        const loadingSpinner = document.getElementById("loadingSpinner");
        const formContainer = document.getElementById("formContainer");

        fetch('./data/fetch_performance_report.php')
            .then(response => response.json())
            .then(data => {
                Object.keys(data).forEach(key => {
                    const input = document.querySelector(`input[name='${key}']`);
                    if (input) input.value = data[key] || "Not available";
                });

                document.querySelectorAll(".month").forEach(el => el.textContent = data.currentMonth);
                document.querySelectorAll(".year").forEach(el => el.textContent = data.currentYear);
                document.querySelector(".username").textContent = data.user;

                loadingSpinner.style.display = 'none';
                formContainer.style.display = 'block';

                const dealsInYear = data.dealsInYear;
                console.log(dealsInYear);
                const dealsTable = document.getElementById("dealsTable");

                dealsInYear.forEach(deal => {
                    const row = document.createElement("tr");
                    row.classList.add("border-t", "dark:border-gray-500");
                    row.innerHTML = `
                        <td class="p-4">${deal.TITLE}</td>
                        <td class="p-4">${deal.OPPORTUNITY}</td>
                        <td class="p-4">${deal.TYPE_ID}</td>
                        <td class="p-4">${deal.UF_CRM_1727628122686}</td>
                        <td class="p-4">${formatDate(deal.DATE_CREATE)}</td>
                    `;
                    dealsTable.appendChild(row);
                });
            })
            .catch(error => {
                console.error("Error fetching data:", error);
                loadingSpinner.style.display = 'none';
                formContainer.style.display = 'block';
            });
    });
</script>

<?php include('includes/footer.php'); ?>