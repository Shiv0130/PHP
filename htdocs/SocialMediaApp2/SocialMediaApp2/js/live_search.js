

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search_term');
    const searchSuggestionsDiv = document.getElementById('searchSuggestions');
    const suggestionsList = searchSuggestionsDiv ? searchSuggestionsDiv.querySelector('ul') : null;
    const userSearchForm = document.getElementById('userSearchForm'); 

    // Debounce function to limit how often AJAX requests are made
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    };

    // Function to fetch and display suggestions
    const fetchSuggestions = async (searchTerm) => {
        if (!suggestionsList || searchTerm.length < 2) { // Only search if term is at least 2 chars
            suggestionsList.innerHTML = ''; // Clear suggestions if search term is too short
            searchSuggestionsDiv.style.display = 'none';
            return;
        }

        try {
            // Construct the URL for the AJAX request
            const response = await fetch(`ajax_search_users.php?search_term=${encodeURIComponent(searchTerm)}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const users = await response.json();

            // Clear previous suggestions
            suggestionsList.innerHTML = '';

            if (users.length > 0) {
                users.forEach(user => {
                    const listItem = document.createElement('li');
                    listItem.innerHTML = `
                        <div class="search-suggestion-item">
                            <img src="${user.profile_picture ? '../' + user.profile_picture : '../images/default_profile.png'}" alt="Profile Pic" class="profile-pic-small">
                            <span>${user.full_name}</span>
                            <small>(${user.email})</small>
                        </div>
                    `;
                 
                    listItem.addEventListener('click', () => {
                        searchInput.value = user.full_name; // Populate input with name
                        searchSuggestionsDiv.style.display = 'none';
                    });
                    suggestionsList.appendChild(listItem);
                });
                searchSuggestionsDiv.style.display = 'block'; 
            } else {
                suggestionsList.innerHTML = '<li>No suggestions found.</li>';
                searchSuggestionsDiv.style.display = 'block';
            }

        } catch (error) {
            console.error("Error fetching search suggestions:", error);
            suggestionsList.innerHTML = '<li>Error loading suggestions.</li>';
            searchSuggestionsDiv.style.display = 'block';
        }
    };

    // Add event listener for input
    if (searchInput && suggestionsList) {
        // Use debounced function to call fetchSuggestions after user stops typing for 300ms
        const debouncedFetch = debounce(fetchSuggestions, 300);

        searchInput.addEventListener('input', (event) => {
            debouncedFetch(event.target.value);
        });

        // Hide suggestions when clicking outside of the search input or suggestions list
        document.addEventListener('click', (event) => {
            if (!searchSuggestionsDiv.contains(event.target) && !searchInput.contains(event.target)) {
                searchSuggestionsDiv.style.display = 'none';
            }
        });
        userSearchForm.addEventListener('submit', (event) => {

            searchSuggestionsDiv.style.display = 'none';
        });
    }
});