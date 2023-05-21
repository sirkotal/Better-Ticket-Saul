function showAgents() {
  const agentButtons = document.querySelectorAll('.agent-button');

  agentButtons.forEach(button => {
    button.addEventListener('click', () => {
      const agentSection = button.parentNode.parentNode;
      const agentData = agentSection.querySelectorAll('.agent-data');
      const icon = button.querySelector('i');

      agentData.forEach((data) => {
        data.classList.toggle('show');
      });

      icon.classList.toggle('fa-caret-square-down');
      icon.classList.toggle('fa-caret-square-up');
    });
  });
}
showAgents();
