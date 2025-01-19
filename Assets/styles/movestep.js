document.addEventListener("DOMContentLoaded", () => {
  const tabButtons = document.querySelectorAll(".tab-button");
  const stepContents = document.querySelectorAll(".step-content");

  tabButtons.forEach(button => {
    button.addEventListener("click", () => {
      // Remove active class from all buttons
      tabButtons.forEach(btn => btn.classList.remove("active"));

      // Add active class to the clicked button
      button.classList.add("active");

      // Hide all step contents
      stepContents.forEach(content => content.classList.add("hidden"));

      // Show the corresponding step content
      const stepId = button.getAttribute("data-step");
      document.getElementById(`step-${stepId}`).classList.remove("hidden");
    });
  });
});
