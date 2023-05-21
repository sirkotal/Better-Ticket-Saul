function showAgents() {
  const agentButtons = document.querySelectorAll('.agent-button');

  agentButtons.forEach(button => {
    button.addEventListener('click', () => {
      const agentSection = button.parentNode.parentNode;
      const agentData = agentSection.querySelectorAll('.agent-data');

      agentData.forEach((data) => {
        data.classList.toggle('show');
      });
    });
  });
}
showAgents();
