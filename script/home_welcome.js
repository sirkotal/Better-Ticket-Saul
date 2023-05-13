function greetUser() {
    let isLoggedIn = false; // TODO
    const greetingText = document.getElementById("saul-greeting");
    const greetingTitle = document.getElementById("saul-title");

    fetch('/api/check_login.php')
        .then(response => response.json())
        .then(data => {
            isLoggedIn = data.isLoggedIn;
            if (isLoggedIn) {
                fetch('/api/get_username.php')
                    .then(response => response.json())
                    .then(data => {
                        console.log(data)
                        const userName = data.name;
                        greetingTitle.innerHTML = `Hello There, ${userName}!`
                        greetingText.innerHTML = `Our system allows you to submit and track trouble tickets related to technical issues, 
                        customer support, or any other inquiries. Please use the links above to submit a ticket, view your tickets, check the FAQs,
                        or contact us for assistance.`;
                    })
                    .catch(error => {
                        console.error('Error fetching the username:', error);
                        greetingText.innerHTML = `Our system allows you to submit and track trouble tickets related to technical issues, 
                        customer support, or any other inquiries. Please use the links above to submit a ticket, view your tickets, check the FAQs,
                        or contact us for assistance.`;
                    });
            } 
            else {
                greetingTitle.innerHTML = `Welcome to Better Ticket Saul`
                greetingText.innerHTML = `Our system allows you to submit and track trouble tickets related to technical issues, 
                customer support, or any other inquiries. Please login into your account or create one to be able to submit and view your own 
                tickets; alternatively, you can check the FAQs or contact us for assistance.`;
            }
        })
    .catch(error => {
        console.error('Error checking if user is logged in:', error);
        greetingTitle.innerHTML = `Welcome to Better Ticket Saul`
        greetingText.innerHTML = `Our system allows you to submit and track trouble tickets related to technical issues, 
        customer support, or any other inquiries. Please login into your account or create one to be able to submit and view your own 
        tickets; alternatively, you can check the FAQs or contact us for assistance.`;
    });
}    

greetUser();