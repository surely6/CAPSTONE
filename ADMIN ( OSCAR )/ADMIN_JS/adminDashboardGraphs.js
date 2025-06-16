
let studentChart;
let moduleChart;
let quizChart;
let customLegend;
let customLegend2;
let customLegend3;


// custom legend where locationID is the container and chartName is used to obtain the datasets' name and color
function generateLegend(locationID, chartName){
    const location = document.getElementById(locationID);

    const div = document.createElement('DIV');
    div.setAttribute('id', 'customLegend');

    const ul = document.createElement('UL');

    chartName.legend.legendItems.forEach((dataset) => {
        const text = dataset.text;
        const bgColor = dataset.fillStyle;
        const bColor = dataset.strokeStyle;

        const li = document.createElement('LI');

        const spanBox = document.createElement('SPAN');
        spanBox.style.borderColor = bColor;
        spanBox.style.backgroundColor = bgColor;

        const p = document.createElement('P');
        const textNode = document.createTextNode(text);


        ul.appendChild(li);
        li.appendChild(spanBox);
        li.appendChild(p);
        p.appendChild(textNode);
    });

    location.appendChild(div);
    div.appendChild(ul);

    return div;
}

// removing previous legend on click
function killLegend(legendElement) {
    if (legendElement && legendElement.parentNode) {
        legendElement.parentNode.removeChild(legendElement);
    }
}

// generating graph
function getGraphData(graphType){
    fetch(`adminDashboardGraphs.php?graphType=${encodeURIComponent(graphType)}`)
    .then(res => res.json())
    .then(data => {
        const ctx = document.getElementById('StudentChart');
        const ctx2 = document.getElementById('ModuleChart');
        const ctx3 = document.getElementById('QuizChart');
        const legendLoca = 'StudentDataNLegend';
        const legendLoca2 = 'ModuleDataNLegend';
        const legendLoca3 = 'QuizDataNLegend';

    // set bar colors here
    const backgroundColor =
    [
        'rgb(54, 162, 235)',
        'rgb(255, 99, 132)',
        'rgb(255, 205, 86)',
        'rgb(75, 192, 192)',
        'rgb(153, 102, 255)',
        'rgb(255, 159, 64)',
        'rgb(201, 203, 207)',
        'rgb(100, 255, 218)',
        'rgb(140, 120, 255)',
        'rgb(255, 99, 71)',
        'rgb(0, 200, 83)',
        'rgb(220, 20, 60)',
        'rgb(0, 123, 255)'
    ];
    
    // dataset format
    const datasets = data.labels.map((label, index) => ({
        label: label,
        data: [data.values[index]], 
        borderWidth: 1,
        backgroundColor: [backgroundColor[index]]
    }));

    // Student

    if(graphType === 'studentLearningStyle' || graphType === 'studentForm'){

        if(studentChart){
            studentChart.destroy();
            console.log('STUDENT GRAPH DELETED')
        }

        studentChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [''],
            datasets: datasets,
        },
        options: {
        responsive: true,
        indexAxis: 'y',
            scales: {
            y: {
                beginAtZero: true
            }
            },
            plugins: {
                legend:{
                    display: false,
                }
            }
        }
        });
        console.log('STUDENT GRAPH GENERATED')

        
        if(customLegend){
            killLegend(customLegend);   
            console.log('STUDENT LEGEND SLAIN');
        }

        customLegend = generateLegend(legendLoca, studentChart);
        console.log('STUDENT LEGEND BORN');
    }

    if(graphType === 'moduleLearningStyle' || graphType === 'moduleForm' || graphType === 'moduleSubject'){

        if(moduleChart){
            moduleChart.destroy();
            console.log('MODULE GRAPH DELETED')
        }

        moduleChart = new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: [''],
            datasets: datasets,
        },
        options: {
        responsive: true,
        indexAxis: 'y',
            scales: {
            y: {
                beginAtZero: true
            }
            },
            plugins: {
                legend:{
                    display: false,
                }
            }
        }
        });
        console.log('MODULE GRAPH GENERATED')

        if(customLegend2){
            killLegend(customLegend2);   
            console.log('MODULE LEGEND SLAIN');
        }

        customLegend2 = generateLegend(legendLoca2, moduleChart);
        console.log('MODULE LEGEND BORN');

    }

    if(graphType === 'quizForm' || graphType === 'quizSubject'){

        if(quizChart){
            quizChart.destroy();
            console.log('QUIZ GRAPH DELETED')
        }

        quizChart = new Chart(ctx3, {
        type: 'bar',
        data: {
            labels: [''],
            datasets: datasets,
        },
        options: {
        responsive: true,
        indexAxis: 'y',
            scales: {
            y: {
                beginAtZero: true
            }
            },
            plugins: {
                legend:{
                    display: false,
                }
            }
        }
        });
        console.log('QUIZ GRAPH GENERATED')
        
        if(customLegend3){
            killLegend(customLegend3);   
            console.log('QUIZ LEGEND SLAIN');
        }

        customLegend3 = generateLegend(legendLoca3, quizChart);
        console.log('QUIZ LEGEND BORN');
    }
    });
}

// button stuff ignore
function setButton(location, index) {
    let count = -1;
    let Buttons = document.querySelectorAll(location);
    Buttons[index].style.borderBottom = ('4px #414443 solid');
    Buttons.forEach(button => {
        count++;
        if (count != index) {
            button.style.borderStyle = ('none');
        }
    });
}