document.addEventListener('DOMContentLoaded', function () {
    var socialIcon = document.querySelector('.social-icon');
    var dropdown = document.querySelector('.dropdown');
    var isDropdownVisible = false;

    socialIcon.addEventListener('click', function (event) {
        event.stopPropagation();
        isDropdownVisible = !isDropdownVisible;
        dropdown.style.display = isDropdownVisible ? 'block' : 'none';
    });

    document.addEventListener('click', function () {
        if (isDropdownVisible) {
            dropdown.style.display = 'none';
            isDropdownVisible = false;
        }
    });
});


// Validation for number input
function validateNumberInput(input) {
    // Ensure the input value is a valid number
    if (isNaN(input.value)) {
        input.setCustomValidity("Please enter a valid number.");
    } else {
        input.setCustomValidity(""); // Clear the custom validity message
    }
}

// Change items per page
function changeItemsPerPage() {
    var select = document.getElementById("itemsPerPage");
    var selectedValue = select.options[select.selectedIndex].value;
    window.location.href = "?page=<?= $current_page ?>&itemsPerPage=" + selectedValue;
}

// Search table function
function searchTable() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("searchInput");
    filter = input.value.toUpperCase();
    table = document.getElementById("myTable");
    tr = table.getElementsByTagName("tr");

    for (i = 1; i < tr.length; i++) { // Start from index 1 to skip the header row
        td = tr[i].getElementsByTagName("td");
        var found = false;
        for (var j = 0; j < td.length; j++) {
            txtValue = td[j].textContent || td[j].innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                found = true;
                break;
            }
        }
        tr[i].style.display = found ? "" : "none";
    }
}
