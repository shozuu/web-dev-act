// products.js

let deleteButtons = document.querySelectorAll('.delete-button');

deleteButtons.forEach(button => {
    button.addEventListener('click', () => {
        const id = button.dataset.id;
        const productName = button.dataset.name;

        let response = confirm(`Do you want to delete the product ${productName}?`)

        if (response) {
            fetch(`deleteProduct.php?id=${id}`, {
                method: 'GET'
                // this redirects us to deleteProduct.php and pass the id as parameter to be used in deletion
            })
            .then(response => response.text())
            .then(status => {
                if (status) {
                    window.location.href = 'products.php';
                }
            })
        }
    })
});