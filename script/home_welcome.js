function greetUser() {
    const isLoggedIn = true; // TODO
    const greetingText = document.getElementById("saul-greeting");
    const greetingTitle = document.getElementById("saul-title");
  
    if (isLoggedIn) {
        greetingTitle.innerHTML = `Hello There, ${userName}!`
        greetingText.innerHTML = `Our system allows you to submit and track trouble tickets related to technical issues, 
        customer support, or any other inquiries. Please use the links above to submit a ticket, view your tickets, check the FAQs,
        or contact us for assistance.`;
    } 
    else {
        greetingTitle.innerHTML = `Welcome to Better Ticket Saul`
        greetingText.innerHTML = `Our system allows you to submit and track trouble tickets related to technical issues, 
        customer support, or any other inquiries. Please login into your account or create one to be able to submit and view your own 
        tickets; alternatively, you can check the FAQs or contact us for assistance.`;
    }
}
  