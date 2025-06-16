let dataChart;
let customLegend;

// custom legend where locationID is the container and chartName is used to obtain the datasets' name and color
function generateLegend(locationID, chartName) {
  const location = document.getElementById(locationID);

  const div = document.createElement("DIV");
  div.setAttribute("id", "customLegend");

  const ul = document.createElement("UL");

  chartName.legend.legendItems.forEach((dataset) => {
    const text = dataset.text;
    const bgColor = dataset.fillStyle;
    const bColor = dataset.strokeStyle;

    const li = document.createElement("LI");

    const spanBox = document.createElement("SPAN");
    spanBox.style.borderColor = bColor;
    spanBox.style.backgroundColor = bgColor;

    const p = document.createElement("P");
    const textNode = document.createTextNode(text);

    ul.appendChild(li);
    li.appendChild(spanBox);
    li.appendChild(p);
    p.appendChild(textNode);
  });

  location.appendChild(div);
  div.appendChild(ul);
}

// generating graph
function getGraphData(data) {
  console.log(data);
  const ctx = document.getElementById("userChart");
  const legendLoca = "userDataNLegend";

  // set bar colors here
  const backgroundColor = ["rgb(255, 0, 0)", "rgb(0, 255, 0)"];

  // dataset format
  const datasets = data.labels.map((label, index) => ({
    label: label,
    data: [data.values[index]],
    borderWidth: 1,
    backgroundColor: [backgroundColor[index]],
  }));

  dataChart = new Chart(ctx, {
    type: "bar",
    data: {
      labels: [""],
      datasets: datasets,
    },
    options: {
      responsive: true,
      indexAxis: "y",
      scales: {
        y: {
          beginAtZero: true,
        },
      },
      plugins: {
        legend: {
          display: false,
        },
      },
    },
  });
  console.log("GRAPH GENERATED");

  generateLegend(legendLoca, dataChart);
}
