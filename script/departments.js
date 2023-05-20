function showAgents() {
    const agentButtons = document.querySelectorAll('.agent-button');
  
    agentButtons.forEach(button => {
      button.addEventListener('click', () => {
        const agentSection = button.parentNode.parentNode;
        const agentData = agentSection.querySelector('.agent-data');
  
        agentData.classList.toggle('show');
      });
    });
}
  
showAgents();