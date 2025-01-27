const now = new Date();

const loader = document.createElement("div");
loader.classList.add("preloader");
loader.innerHTML = `<div class="lds-ring"><div></div><div></div><div></div><div></div></div>`;

window.end_loader = function () {
  document.querySelectorAll(".preloader").forEach((el) => {
    el.remove();
  });
};

window.addEventListener("beforeunload", function (e) {
  e.preventDefault();
  // start_loader()
});

window.addEventListener("DOMContentLoaded", function () {
  // Ensure that the element with id "dt-year" exists before setting its innerHTML
  const dtYearElement = document.getElementById("dt-year");
  if (dtYearElement) {
    dtYearElement.innerHTML = now.getFullYear();
  } else {
    console.error('Element with id "dt-year" not found!');
  }
});
