document.addEventListener('DOMContentLoaded', function() {
    var selectedGroup = document.getElementById('select-groups').value;
    var loader = document.getElementById('loader');
    var content = document.getElementById('content');
    // Create a new XMLHttpRequest object
    var xhr = new XMLHttpRequest();
    // Configure the request
    xhr.open('GET', '/plateforme/groups/'+ selectedGroup +'/getdata', true);
    // Set up a callback function to handle the response
    xhr.onload = function() {
        if (xhr.status >= 200 && xhr.status < 300) { // Parse the JSON response
            var data = JSON.parse(xhr.responseText);
            // Call a function to update the page with the data
            updateDataInscritPerDate(data.learnersInscriptionsPerStatDate, data.timingDetailsPerStatDate);
            updateDataInscrit(data.learnersInscriptions, data.timingDetails, data.learnersCharts)
            updateSoftModules(data.softStats);
            updateDigitalModules(data.digitalStats);
            updateSMModules(data.smStats);
            updateMoocModules(data.moocStats);
            updateLanguageTiming(selectedGroup, data.speexStats)
            updateChartTiming(data.timingChart)
            updateLps(data.lpStats)
            updateLsc(data.lscStats)
            // Hide the loader and display the content
            loader.classList.add('d-none');
            content.classList.remove('d-none');

        } else {
            console.error('Request failed with status:', xhr.status);
        }
    };
    // Send the request
    xhr.send();
});

document.getElementById('select-groups').addEventListener('change', function () {
    var selectedGroup = document.getElementById('select-groups').value;
    var loader = document.getElementById('loader');
    var content = document.getElementById('content');

    loader.classList.remove('d-none');
    content.classList.add('d-none');
    // Create a new XMLHttpRequest object
    var xhr = new XMLHttpRequest();
    // Configure the request
    xhr.open('GET', '/plateforme/groups/'+ selectedGroup +'/getdata', true);
    // Set up a callback function to handle the response
    xhr.onload = function() {
        if (xhr.status >= 200 && xhr.status < 300) { // Parse the JSON response
            var data = JSON.parse(xhr.responseText);

            // Call a function to update the page with the data
            updateDataInscritPerDate(data.learnersInscriptionsPerStatDate, data.timingDetailsPerStatDate);
            updateDataInscrit(data.learnersInscriptions, data.timingDetails, data.learnersCharts)
            updateSoftModules(data.softStats);
            updateDigitalModules(data.digitalStats);
            updateMoocModules(data.moocStats);
            updateLanguageTiming(selectedGroup, data.speexStats)
            updateChartTiming(data.timingChart)
            updateLps(data.lpStats)
            updateLsc(data.lscStats)
            // Hide the loader and display the content
            loader.classList.add('d-none');
            content.classList.remove('d-none');

        } else {
            console.error('Request failed with status:', xhr.status);
        }
    };
    // Send the request
    xhr.send();
});

document.getElementById('select-langues').addEventListener('change', function () {
    var loaderLG = document.getElementById('loaderLG');
    var contentLG = document.getElementById('contentLG');
    loaderLG.classList.remove('d-none');
    contentLG.classList.add('d-none');

    var selectedLangue = document.getElementById('select-langues').value;
    var selectedGroup = document.getElementById('select-groups').value;
    updateLanguageChart(selectedGroup, selectedLangue);
});


document.getElementById('select-enis').addEventListener('change', function () {
    var selectedDigital = document.getElementById('select-enis').value;
    var selectedGroup = document.getElementById('select-groups').value;
    updateDigitalModule(selectedGroup,selectedDigital);
});
document.getElementById('btnEniReload').addEventListener('click', function () {
    var selectedGroup = document.getElementById('select-groups').value;
    updateDigitalModule(selectedGroup, null);
});


document.getElementById('select-lps').addEventListener('change', function () {
    var selectedLp = document.getElementById('select-lps').value;
    var selectedGroup = document.getElementById('select-groups').value;
    updateLpData(selectedGroup,selectedLp);
});
document.getElementById('btnFtReload').addEventListener('click', function () {
    var selectedGroup = document.getElementById('select-groups').value;
    updateLpData(selectedGroup,null);
});

document.getElementById('btnInsFilter').addEventListener('click', function () {
    var startDateInput = document.getElementById('insStartDate');
    var endDateInput = document.getElementById('insEndDate');

    var startDateValue = startDateInput.value;
    var endDateValue = endDateInput.value;

    if (startDateValue === '' && endDateValue === '') {
        Swal.fire({
            icon: 'error',
            text: 'Les champs dates ne doivent pas être vide!',
            confirmButtonText: 'Retour',
            confirmButtonColor: "#206BC4"
        });
        return;
    }

    var formattedStartDate = formatDate(startDateValue);
    var formattedEndDate = formatDate(endDateValue);
    var selectedGroup = document.getElementById('select-groups').value;

    var loaderIns = document.getElementById('loaderInscrits');
    var contentIns = document.getElementById('contentInscrits');
    loaderIns.classList.remove('d-none');
    contentIns.classList.add('d-none');

    var xhr = new XMLHttpRequest();
    xhr.open('GET', '/plateforme/groups/'+ selectedGroup +'/getinscritsdata/filter?start_date=' + formattedStartDate + '&end_date=' + formattedEndDate, true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                var data = JSON.parse(xhr.responseText);
                updateDataInscrit(data.learnersInscriptions, data.timingDetails, data.learnersCharts)
                contentIns.classList.remove('d-none');
                loaderIns.classList.add('d-none');
            } else {
                console.error('Request failed with status:', xhr.status);
            }
        }
    };
    xhr.send();
});
document.getElementById('btnInsReload').addEventListener('click', function () {
    var startDateInput = document.getElementById('insStartDate');
    var endDateInput = document.getElementById('insEndDate');
    startDateInput.value = ''; // Clears the value of the start date input
    endDateInput.value = ''; // Clears the value of the end date input
    var selectedGroup = document.getElementById('select-groups').value;

    var loaderIns = document.getElementById('loaderInscrits');
    var contentIns = document.getElementById('contentInscrits');
    loaderIns.classList.remove('d-none');
    contentIns.classList.add('d-none');

    var xhr = new XMLHttpRequest();
    xhr.open('GET', '/plateforme/groups/'+ selectedGroup +'/getinscritsdata/filter', true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                var data = JSON.parse(xhr.responseText);
                updateDataInscrit(data.learnersInscriptions, data.timingDetails, data.learnersCharts)
                contentIns.classList.remove('d-none');
                loaderIns.classList.add('d-none');
            } else {
                console.error('Request failed with status:', xhr.status);
            }
        }
    };
    xhr.send();
});

document.getElementById('btnLscFilter').addEventListener('click', function () {
    var startLscDateInput = document.getElementById('lscStartDate');
    var endLscDateInput = document.getElementById('lscEndDate');
    var selectedGroup = document.getElementById('select-groups').value;
    var startLscDateValue = startLscDateInput.value;
    var endLscDateValue = endLscDateInput.value;

    if (startLscDateValue === '' && endLscDateValue === '') {
        Swal.fire({
            icon: 'error',
            text: 'Les champs dates ne doivent pas être vide!',
            confirmButtonText: 'Retour',
            confirmButtonColor: "#206BC4"
        });
        return;
    }


    var loaderLsc = document.getElementById('loaderLsc');
    var contentLsc = document.getElementById('contentLsc');
    loaderLsc.classList.remove('d-none');
    contentLsc.classList.add('d-none');


    var formattedLscStartDate = formatDate(startLscDateValue);
    var formattedLscEndDate = formatDate(endLscDateValue);


    var xhr = new XMLHttpRequest();
    xhr.open('GET', '/plateforme/groups/'+ selectedGroup +'/getlscdata/filter?start_date=' + formattedLscStartDate + '&end_date=' + formattedLscEndDate, true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                var data = JSON.parse(xhr.responseText);
                updateLsc(data)
                contentLsc.classList.remove('d-none');
                loaderLsc.classList.add('d-none');
            } else {
                console.error('Request failed with status:', xhr.status);
            }
        }
    };
    xhr.send();
});
document.getElementById('btnLscReload').addEventListener('click', function () {
    var startLscDateInput = document.getElementById('lscStartDate');
    var endLscDateInput = document.getElementById('lscEndDate');
    startLscDateInput.value = ''; // Clears the value of the start date input
    endLscDateInput.value = ''; // Clears the value of the end date input
    var selectedGroup = document.getElementById('select-groups').value;

    var loaderLsc = document.getElementById('loaderLsc');
    var contentLsc = document.getElementById('contentLsc');
    loaderLsc.classList.remove('d-none');
    contentLsc.classList.add('d-none');


    var xhr = new XMLHttpRequest();
    xhr.open('GET', '/plateforme/groups/'+ selectedGroup +'/getlscdata/filter', true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                var data = JSON.parse(xhr.responseText);
                updateLsc(data)
                contentLsc.classList.remove('d-none');
                loaderLsc.classList.add('d-none');
            } else {
                console.error('Request failed with status:', xhr.status);
            }
        }
    };
    xhr.send();
});

$smsSelect = document.getElementById('select-sms');
if($smsSelect != null){

    document.getElementById('select-sms').addEventListener('change', function () {
        var selectedGroup= document.getElementById('select-groups').value;
        var selectedSM = document.getElementById('select-sms').value;
        updateSMModule(selectedGroup,selectedSM);
    });
    document.getElementById('btnSMReload').addEventListener('click', function () {
        var selectedGroup = document.getElementById('select-groups').value;
        updateSMModule(selectedGroup,null);
    });
}


function updateDataInscritPerDate(learnersInscriptionsPerStatDate, timingDetailsPerStatDate) {
    var inscritsAY = document.getElementById('inscritsAY');
    var actifsAY = document.getElementById('actifsAY');
    var inactifsAY = document.getElementById('inactifsAY');
    var sessionAY = document.getElementById('sessionAY');
    var avgsessionAY = document.getElementById('avgsessionAY');
    var cmiAY = document.getElementById('cmiAY');
    var avgcmiAY = document.getElementById('avgcmiAY');
    var tcAY = document.getElementById('tcAY');
    var avgtcAY = document.getElementById('avgtcAY');
    var tprAY = document.getElementById('tprAY');
    var avgtprAY = document.getElementById('avgtprAY');

    if(inscritsAY != null){
        inscritsAY.textContent = learnersInscriptionsPerStatDate.total;
    }
    if(actifsAY != null){
        actifsAY.textContent = learnersInscriptionsPerStatDate.active;
    }
    if(inactifsAY != null){
        inactifsAY.textContent = learnersInscriptionsPerStatDate.inactive;
    }
    if(sessionAY != null){
        sessionAY.textContent = timingDetailsPerStatDate.total_session_time;
    }
    if(avgsessionAY != null){
        avgsessionAY.textContent = timingDetailsPerStatDate.avg_session_time;
    }
    if(cmiAY != null){
        cmiAY.textContent = timingDetailsPerStatDate.total_cmi_time;
    }
    if(avgcmiAY != null){
        avgcmiAY.textContent = timingDetailsPerStatDate.avg_cmi_time;
    }
    if(tcAY != null){
        tcAY.textContent = timingDetailsPerStatDate.total_calculated_time;
    }
    if(avgtcAY != null){
        avgtcAY.textContent = timingDetailsPerStatDate.avg_calculated_time;
    }
    if(tprAY != null){
        tprAY.textContent = timingDetailsPerStatDate.total_recommended_time;
    }
    if(avgtprAY != null){
        avgtprAY.textContent = timingDetailsPerStatDate.avg_recommended_time;
    }
}

function updateDataInscrit(learnersInscriptions, timingDetails, learnersCharts) {
    var inscrits = document.getElementById('inscrits');
    var actifs = document.getElementById('actifs');
    var inactifs = document.getElementById('inactifs');
    var archives = document.getElementById('archives');
    var session = document.getElementById('session');
    var avgsession = document.getElementById('avgsession');
    var cmi = document.getElementById('cmi');
    var avgcmi = document.getElementById('avgcmi');
    var tc = document.getElementById('tc');
    var avgtc = document.getElementById('avgtc');
    var tpr = document.getElementById('tpr');
    var avgtpr = document.getElementById('avgtpr');

    inscrits.textContent = learnersInscriptions.total;
    actifs.textContent = learnersInscriptions.active;
    inactifs.textContent = learnersInscriptions.inactive;
    if(archives != null){
        archives.textContent = learnersInscriptions.archive;
    }
    session.textContent = timingDetails.total_session_time;
    avgsession.textContent = timingDetails.avg_session_time;
    cmi.textContent = timingDetails.total_cmi_time;
    avgcmi.textContent = timingDetails.avg_cmi_time;
    tc.textContent = timingDetails.total_calculated_time;
    avgtc.textContent = timingDetails.avg_calculated_time;
    tpr.textContent = timingDetails.total_recommended_time;
    avgtpr.textContent = timingDetails.avg_recommended_time;

    if(learnersCharts.chartInscritPerCategorie != null)
    {
        window.ApexCharts && (new ApexCharts(document.getElementById('chart-demo-pie'), {
            chart: {
                type: "donut",
                fontFamily: 'inherit',
                height: 240,
                sparkline: {
                    enabled: true
                },
                animations: {
                    enabled: true
                }
            },
            fill: {
                opacity: 1
            },
            series: learnersCharts.chartInscritPerCategorie.data,
            labels: learnersCharts.chartInscritPerCategorie.labels,
            tooltip: {
                theme: 'dark'
            },
            grid: {
                strokeDashArray: 4
            },
            colors: ["#1676FB", "#798bff", "#6b5b95", "#b8acff", "#f9db7b", "#1EE0AC", "#ffa9ce"],
            legend: {
                show: true,
                position: 'bottom',
                offsetY: 12,
                markers: {
                    width: 10,
                    height: 10,
                    radius: 100
                },
                itemMargin: {
                    horizontal: 8,
                    vertical: 8
                }
            },
            tooltip: {
                fillSeriesColor: false
            }
        })).render();
    }
    if(learnersCharts.chartInscritPerCategoryAndStatus != null)
    {
        window.ApexCharts && (new ApexCharts(document.getElementById('chart-completion-tasks-9'),
        {
            chart: {
                type: "bar",
                fontFamily: 'inherit',
                height: 240,
                parentHeightOffset: 0,
                toolbar: {
                    show: false
                },
                animations: {
                    enabled: true
                },
                stacked: true
            },
            plotOptions: {
                bar: {
                    columnWidth: '50%'
                }
            },
            dataLabels: {
                enabled: false
            },
            fill: {
                opacity: 1
            },
            series: [{
                name: "actif",
                data: learnersCharts.chartInscritPerCategoryAndStatus.actives
            }, {
                name: "inactive",
                data: learnersCharts.chartInscritPerCategoryAndStatus.inactives
            }],
            tooltip: {
                theme: 'dark'
            },
            grid: {
                padding: {
                    top: -20,
                    right: 0,
                    left: -4,
                    bottom: -4
                },
                strokeDashArray: 4
            },
            xaxis: {
                labels: {
                    padding: 0
                },
                tooltip: {
                    enabled: false
                },
                axisBorder: {
                    show: false
                }
            },
            yaxis: {
                labels: {
                    padding: 4
                }
            },
            labels: learnersCharts.chartInscritPerCategoryAndStatus.labels,
            colors: [
                tabler.getColor("green"), tabler.getColor("red")
            ],
            legend: {
                show: true
            }
        }
        )).render();
    }
}

function updateSoftModules(softStats){
    var sessionSoft = document.getElementById('sessionSoft');
    var cmiSoft = document.getElementById('cmiSoft');
    var tcSoft = document.getElementById('tcSoft');
    var trSoft = document.getElementById('trSoft');
    var insSoftT = document.getElementById('insSoftT');
    var insSoftND = document.getElementById('insSoftND');
    var insSoftP = document.getElementById('insSoftP');

    sessionSoft.textContent = softStats.statSoftTimes.total_session_time;
    cmiSoft.textContent = softStats.statSoftTimes.total_cmi_time;
    tcSoft.textContent = softStats.statSoftTimes.total_calculated_time;
    trSoft.textContent = softStats.statSoftTimes.total_recommended_time;
    insSoftT.textContent = softStats.statSoftskills.completed;
    insSoftND.textContent = softStats.statSoftskills.enrolled;
    insSoftP.textContent= softStats.statSoftskills.in_progress;

    window.ApexCharts && (new ApexCharts(document.getElementById('chart-softs'), {
        chart: {
            type: "bar",
            fontFamily: 'inherit',
            height: 240,
            parentHeightOffset: 0,
            toolbar: {
                show: false,
            },
            animations: {
                enabled: false
            },
        },
        plotOptions: {
            bar: {
                barHeight: '50%',
                horizontal: true,
                dataLabels: {
                    position: 'bottom'
                },
            }
        },
        dataLabels: {
            enabled: true,
            textAnchor: 'start',
            style: {
                colors: ['#000']
            },
            formatter: function (val, opt) {
                return opt.w.globals.labels[opt.dataPointIndex] + ":  " + val + " inscriptions."
            },
            offsetX: 0,
            dropShadow: {
                enabled: false
            }
        },
        stroke: {
            width: 1,
            colors: ['#fff']
        },
        series: [{
            name: "Total d'inscriptions",
            data: softStats.softCharts.data
        }],
        tooltip: {
            theme: 'dark',
            x: {
                show: true
            },
            y: {
                title: {
                    formatter: function () {
                    return ''
                    }
                }
            }
        },
        grid: {
            padding: {
                top: -20,
                right: 0,
                left: -4,
                bottom: -4
            },
            strokeDashArray: 4,
        },
        xaxis: {
            categories: softStats.softCharts.labels,
        },
        yaxis: {
            labels: {
                padding: 4,
                show: false
            },
        },
        colors: ["#b695ff"],
        legend: {
            show: true,
        },
    })).render();
}

function updateMoocModules(moocStats){
    var sessionMc = document.getElementById('sessionMc');
    var cmiMc = document.getElementById('cmiMc');
    var tcMc = document.getElementById('tcMc');
    var trMc = document.getElementById('trMc');
    var insMcT = document.getElementById('insMcT');
    var insMcND = document.getElementById('insMcND');
    var insMcP = document.getElementById('insMcP');
    var insMcW = document.getElementById('insMcW');

    sessionMc.textContent = moocStats.statMoocTimes.total_session_time;
    cmiMc.textContent = moocStats.statMoocTimes.total_cmi_time;
    tcMc.textContent = moocStats.statMoocTimes.total_calculated_time;
    trMc.textContent = moocStats.statMoocTimes.total_recommended_time;
    insMcT.textContent = moocStats.statMooc.completed;
    insMcND.textContent = moocStats.statMooc.enrolled;
    insMcP.textContent= moocStats.statMooc.in_progress;
    var insMcW = moocStats.statMooc.waiting;

    window.ApexCharts && (new ApexCharts(document.getElementById('chart-moocs'), {
        chart: {
            type: "bar",
            fontFamily: 'inherit',
            height: 240,
            parentHeightOffset: 0,
            toolbar: {
                show: false,
            },
            animations: {
                enabled: false
            },
        },
        plotOptions: {
            bar: {
                barHeight: '50%',
                horizontal: true,
                dataLabels: {
                    position: 'bottom'
                },
            }
        },
        dataLabels: {
            enabled: true,
            textAnchor: 'start',
            style: {
                colors: ['#000']
            },
            formatter: function (val, opt) {
                return opt.w.globals.labels[opt.dataPointIndex] + ":  " + val + " inscriptions."
            },
            offsetX: 0,
            dropShadow: {
                enabled: false
            }
        },
        stroke: {
            width: 1,
            colors: ['#fff']
        },
        series: [{
            name: "Total d'inscriptions",
            data: moocStats.moocCharts.data
        }],
        tooltip: {
            theme: 'dark',
            x: {
                show: true
            },
            y: {
                title: {
                    formatter: function () {
                    return ''
                    }
                }
            }
        },
        grid: {
            padding: {
                top: -20,
                right: 0,
                left: -4,
                bottom: -4
            },
            strokeDashArray: 4,
        },
        xaxis: {
            categories: moocStats.moocCharts.labels,
        },
        yaxis: {
            labels: {
                padding: 4,
                show: false
            },
        },
        colors: ["#F9DB7B"],
        legend: {
            show: true,
        },
    })).render();
}

function updateDigitalModules(digitalStats, selectedDigital=null){
    var sessionEni = document.getElementById('sessionEni');
    var cmiEni = document.getElementById('cmiEni');
    var tcEni = document.getElementById('tcEni');
    var trEni = document.getElementById('trEni');
    var insEniT = document.getElementById('insEniT');
    var insEniND = document.getElementById('insEniND');
    var insEniP = document.getElementById('insEniP');

    sessionEni.textContent = digitalStats.statDigitalTimes.total_session_time;
    cmiEni.textContent = digitalStats.statDigitalTimes.total_cmi_time;
    tcEni.textContent = digitalStats.statDigitalTimes.total_calculated_time;
    trEni.textContent = digitalStats.statDigitalTimes.total_recommended_time;
    insEniT.textContent = digitalStats.statDigital.completed;
    insEniND.textContent = digitalStats.statDigital.enrolled;
    insEniP.textContent= digitalStats.statDigital.in_progress;

    document.getElementById('select-enis').innerHTML="";
    document.getElementById('select-enis').insertAdjacentHTML('beforeend', '<option value="" class="text-gray-600">Séléctionner un module</option>');
    digitalStats.modulesDigital.forEach(function(v) {
        var selected = v.docebo_id == selectedDigital ? 'selected' : '';
        var content = '<option value="' + v.docebo_id + '"' + selected + '>' + v.name + '</option>';
        document.getElementById('select-enis').insertAdjacentHTML('beforeend', content);
    });

    window.ApexCharts && (new ApexCharts(document.getElementById('chart-digital'), {
        chart: {
            type: "bar",
            fontFamily: 'inherit',
            height: 240,
            parentHeightOffset: 0,
            toolbar: {
                show: false,
            },
            animations: {
                enabled: false
            },
        },
        plotOptions: {
            bar: {
                barHeight: '50%',
                horizontal: true,
                dataLabels: {
                    position: 'bottom'
                },
            }
        },
        dataLabels: {
            enabled: true,
            textAnchor: 'start',
            style: {
                colors: ['#000']
            },
            formatter: function (val, opt) {
                return opt.w.globals.labels[opt.dataPointIndex] + ":  " + val + " inscriptions."
            },
            offsetX: 0,
            dropShadow: {
                enabled: false
            }
        },
        stroke: {
            width: 1,
            colors: ['#fff']
        },
        series: [{
            name: "Total d'inscriptions",
            data: digitalStats.digitalCharts.data
        }],
        tooltip: {
            theme: 'dark',
            x: {
                show: true
            },
            y: {
                title: {
                    formatter: function () {
                    return ''
                    }
                }
            }
        },
        grid: {
            padding: {
                top: -20,
                right: 0,
                left: -4,
                bottom: -4
            },
            strokeDashArray: 4,
        },
        xaxis: {
            categories: digitalStats.digitalCharts.labels,
        },
        yaxis: {
            labels: {
                padding: 4,
                show: false
            },
        },
        colors: ["#f4AAA4"],
        legend: {
            show: true,
        },
    })).render();
}

function updateLanguageTiming(selectedProject, speexStats){
    var sessionSpeex = document.getElementById('sessionSpeex');
    var cmiSpeex = document.getElementById('cmiSpeex');
    var tcSpeex = document.getElementById('tcSpeex');
    var trSpeex = document.getElementById('trSpeex');
    var insSpeexT = document.getElementById('insSpeexT');
    var insSpeexND = document.getElementById('insSpeexND');
    var insSpeexP = document.getElementById('insSpeexP');
    sessionSpeex.textContent = speexStats.statSpeexTimes.total_session_time;
    cmiSpeex.textContent = speexStats.statSpeexTimes.total_cmi_time;
    tcSpeex.textContent = speexStats.statSpeexTimes.total_calculated_time;
    trSpeex.textContent = speexStats.statSpeexTimes.total_recommended_time;
    insSpeexT.textContent = speexStats.statSpeex.completed;
    insSpeexND.textContent = speexStats.statSpeex.enrolled;
    insSpeexP.textContent= speexStats.statSpeex.in_progress;

    document.getElementById('select-langues').innerHTML="";
    speexStats.speexLangues.forEach(function(v) {
        var content = '<option value="' + v + '">' + v + '</option>';
        document.getElementById('select-langues').insertAdjacentHTML('beforeend', content);
    });

    var selectedLangue = document.getElementById('select-langues').value;
    if(!selectedLangue){
        selectedLangue = "null";
    }
    updateLanguageChart(selectedProject, selectedLangue);
}

function updateChartTiming(timingChart){
    window.ApexCharts && (new ApexCharts(document.getElementById('chart-combination'), {
        chart: {
            type: "bar",
            fontFamily: 'inherit',
            height: 320,
            parentHeightOffset: 0,
            toolbar: {
                show: false,
            },
            animations: {
                enabled: false
            },
        },
        plotOptions: {
            bar: {
                columnWidth: '50%',
            }
        },
        dataLabels: {
            enabled: false,
        },
        fill: {
            opacity: 1,
        },
        series: [{
            name: "Temps de session",
            data: timingChart.session
        },{
            name: "Temps d'engagement",
            data: timingChart.cmi
        },{
            name: "Temps calculé",
            data: timingChart.calculated
        },{
            name: "Temps pédagogique recommandé",
            data: timingChart.recommended
        }],
        tooltip: {
            theme: 'dark'
        },
        grid: {
            padding: {
                top: -20,
                right: 0,
                left: -4,
                bottom: -4
            },
            strokeDashArray: 4,
        },
        xaxis: {
            labels: {
                padding: 0,
            },
            tooltip: {
                enabled: false
            },
            axisBorder: {
                show: false,
            },
            categories: timingChart.labels,
        },
        yaxis: {
            labels: {
                padding: 4
            },
        },
        legend: {
            show: true,
        },
    })).render();
}

function updateLps(lpStats, selectedLp=null){
    var sessionLp = document.getElementById('sessionLp');
    var cmiLp = document.getElementById('cmiLp');
    var tcLp = document.getElementById('tcLp');
    var trLp = document.getElementById('trLp');
    var insLpT = document.getElementById('insLpT');
    var insLpND = document.getElementById('insLpND');
    var insLpP = document.getElementById('insLpP');
    var insLpPL = document.getElementById('insLpPL');
    var insLpPG = document.getElementById('insLpPG');

    sessionLp.textContent = lpStats.statLpsTimes.total_session_time;
    cmiLp.textContent = lpStats.statLpsTimes.total_cmi_time;
    tcLp.textContent = lpStats.statLpsTimes.total_calculated_time;
    trLp.textContent = lpStats.statLpsTimes.total_recommended_time;
    insLpT.textContent = lpStats.statLps.completed;
    insLpND.textContent = lpStats.statLps.enrolled;
    insLpP.textContent= lpStats.statLps.in_progress;
    insLpPL.textContent = lpStats.statLps.in_progress_min;
    insLpPG.textContent = lpStats.statLps.in_progress_max;

    document.getElementById('select-lps').innerHTML = "";
    document.getElementById('select-lps').insertAdjacentHTML('beforeend', '<option value="" class="text-gray-600">Séléctionner un plan de formation</option>');
    lpStats.lps.forEach(function(v) {
        var selected = v.docebo_id == selectedLp ? 'selected' : '';
        var content = '<option value="' + v.docebo_id + '"' + selected + '>' + v.name + '</option>';
        document.getElementById('select-lps').insertAdjacentHTML('beforeend', content);
    });

    window.ApexCharts && (new ApexCharts(document.getElementById('chart-lps'), {
        chart: {
            type: "bar",
            fontFamily: 'inherit',
            height: 240,
            parentHeightOffset: 0,
            toolbar: {
                show: false,
            },
            animations: {
                enabled: false
            },
        },
        plotOptions: {
            bar: {
                barHeight: '50%',
                horizontal: true,
                dataLabels: {
                    position: 'bottom'
                },
            }
        },
        dataLabels: {
            enabled: true,
            textAnchor: 'start',
            style: {
                colors: ['#000']
            },
            formatter: function (val, opt) {
                return opt.w.globals.labels[opt.dataPointIndex] + ":  " + val + " inscriptions."
            },
            offsetX: 0,
            dropShadow: {
                enabled: false
            }
        },
        stroke: {
            width: 1,
            colors: ['#fff']
        },
        series: [{
            name: "Total d'inscriptions",
            data: lpStats.lpCharts.data
        }],
        tooltip: {
            theme: 'dark',
            x: {
                show: true
            },
            y: {
                title: {
                    formatter: function () {
                    return ''
                    }
                }
            }
        },
        grid: {
            padding: {
                top: -20,
                right: 0,
                left: -4,
                bottom: -4
            },
            strokeDashArray: 4,
        },
        xaxis: {
            categories: lpStats.lpCharts.labels,
        },
        yaxis: {
            labels: {
                padding: 4,
                show: false
            },
        },
        colors: [tabler.getColor("primary")],
        legend: {
            show: true,
        },
    })).render();
}

function updateLsc(lscStats){
    var tickets = document.getElementById('tickets');
    var calls = document.getElementById('calls');

    tickets.textContent = lscStats.totalTickets;
    calls.textContent = lscStats.totalCalls;
    window.ApexCharts && (new ApexCharts(document.getElementById('chart-ticket-pie'), {
        chart: {
            type: "donut",
            fontFamily: 'inherit',
            height: 240,
            sparkline: {
                enabled: true
            },
            animations: {
                enabled: true
            }
        },
        fill: {
            opacity: 1
        },
        series: lscStats.ticketsCharts.data,
        labels: lscStats.ticketsCharts.labels,
        tooltip: {
            theme: 'dark'
        },
        grid: {
            strokeDashArray: 4
        },
        colors:  ["#1676FB", "#798bff", "#6b5b95", "#b8acff", "#f9db7b", "#1EE0AC", "#ffa9ce"],
        legend: {
            show: true,
            position: 'bottom',
            offsetY: 12,
            markers: {
                width: 10,
                height: 10,
                radius: 100
            },
            itemMargin: {
                horizontal: 8,
                vertical: 8
            }
        },
        tooltip: {
            fillSeriesColor: false
        }
    })).render();

    window.ApexCharts && (new ApexCharts(document.getElementById('chart-calls-sujet-type'), {
        chart: {
            type: "bar",
            fontFamily: 'inherit',
            height: 240,
            parentHeightOffset: 0,
            toolbar: {
                show: false,
            },
            animations: {
                enabled: false
            },
        },
        plotOptions: {
            bar: {
                columnWidth: '50%',
            }
        },
        dataLabels: {
            enabled: false,
        },
        fill: {
            opacity: 1,
        },
        series: [{
            name: "Reçu",
            data: lscStats.callsPerSubjectAndTypeChart.reçu
        },{
            name: "Emis",
            data: lscStats.callsPerSubjectAndTypeChart.emis
        }],
        tooltip: {
            theme: 'dark'
        },
        grid: {
            padding: {
                top: -20,
                right: 0,
                left: -4,
                bottom: -4
            },
            strokeDashArray: 4,
        },
        xaxis: {
            labels: {
                padding: 0,
            },
            tooltip: {
                enabled: false
            },
            axisBorder: {
                show: false,
            },
            categories: lscStats.callsPerSubjectAndTypeChart.labels,
        },
        yaxis: {
            labels: {
                padding: 4
            },
        },
        colors: [tabler.getColor("danger"), tabler.getColor("green")],
        legend: {
            show: true,
        },
    })).render();

    window.ApexCharts && (new ApexCharts(document.getElementById('chart-calls-statut-type'), {
        chart: {
            type: "line",
            fontFamily: 'inherit',
            height: 240,
            parentHeightOffset: 0,
            toolbar: {
                show: false,
            },
            animations: {
                enabled: false
            },
        },
        fill: {
            opacity: 1,
        },
        stroke: {
            width: 2,
            lineCap: "round",
            curve: "smooth",
        },
        series: [{
            name: "Reçu",
            data: lscStats.callsPerStatutAndTypeChart.reçu
        },{
            name: "Emis",
            data: lscStats.callsPerStatutAndTypeChart.emis
        }],
        tooltip: {
            theme: 'dark'
        },
        grid: {
            padding: {
                top: -20,
                right: 0,
                left: -4,
                bottom: -4
            },
            strokeDashArray: 4,
        },
        dataLabels: {
            enabled: true,
        },
        xaxis: {
            labels: {
                padding: 0,
            },
            tooltip: {
                enabled: false
            },
            categories: lscStats.callsPerStatutAndTypeChart.labels,
        },
        yaxis: {
            labels: {
                padding: 4
            },
        },
        colors: [tabler.getColor("danger"), tabler.getColor("green")],
        legend: {
            show: true,
        },
        markers: {
            size: 2
        },
    })).render();


}

function updateLanguageChart(selectedGroup,selectedLangue=null){
    if(!selectedLangue){
        selectedLangue = "null";
    }
    var loaderLG = document.getElementById('loaderLG');
    var contentLG = document.getElementById('contentLG');
    loaderLG.classList.remove('d-none');
    contentLG.classList.add('d-none');
    var xhr = new XMLHttpRequest();
    xhr.open('GET', '/plateforme/groups/'+ selectedGroup +'/getlanguagedata/'+ selectedLangue, true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                var data = JSON.parse(xhr.responseText);
                if(data){
                    window.ApexCharts && (new ApexCharts(document.getElementById('chart-speex'), {
                        chart: {
                            type: "line",
                            fontFamily: 'inherit',
                            height: 320,
                            parentHeightOffset: 0,
                            toolbar: {
                                show: false,
                            },
                            animations: {
                                enabled: false
                            },
                        },
                        fill: {
                            opacity: 1,
                        },
                        stroke: {
                            width: 2,
                            lineCap: "round",
                            curve: "smooth",
                        },
                        series: [{
                            name: "Nombre d'inscrits",
                            data: data.inscrits
                        },{
                            name: "cumul de formation en heures",
                            data: data.heures
                        }],
                        tooltip: {
                            theme: 'dark'
                        },
                        grid: {
                            padding: {
                                top: -20,
                                right: 0,
                                left: -4,
                                bottom: -4
                            },
                            strokeDashArray: 4,
                        },
                        dataLabels: {
                            enabled: true,
                        },
                        xaxis: {
                            labels: {
                                padding: 0,
                            },
                            tooltip: {
                                enabled: false
                            },
                            categories: data.labels,
                        },
                        yaxis: {
                            labels: {
                                padding: 4
                            },
                        },
                        colors: [tabler.getColor("primary"), tabler.getColor("green")],
                        legend: {
                            show: true,
                        },
                        markers: {
                            size: 2
                        },
                    })).render();

                    loaderLG.classList.add('d-none');
                    contentLG.classList.remove('d-none');
                }

            } else {
                console.error('Request failed with status:', xhr.status);
            }
        }
    };
    xhr.send();
}

function updateDigitalModule(selectedGroup,selectedDigital=null){
    var loaderDG = document.getElementById('loaderDG');
    var contentDG = document.getElementById('contentDG');
    loaderDG.classList.remove('d-none');
    contentDG.classList.add('d-none');
    var xhr = new XMLHttpRequest();
    xhr.open('GET', '/plateforme/groups/'+ selectedGroup +'/getdigitaldata/'+ selectedDigital, true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                var data = JSON.parse(xhr.responseText);
                if(data){

                    updateDigitalModules(data,selectedDigital);
                    loaderDG.classList.add('d-none');
                    contentDG.classList.remove('d-none');
                }

            } else {
                console.error('Request failed with status:', xhr.status);
            }
        }
    };
    xhr.send();
}

function updateLpData(selectedGroup, selectedLp=null){
    var loaderLP = document.getElementById('loaderLP');
    var contentLP = document.getElementById('contentLP');
    loaderLP.classList.remove('d-none');
    contentLP.classList.add('d-none');
    var xhr = new XMLHttpRequest();
    xhr.open('GET', '/plateforme/groups/'+ selectedGroup +'/getlpdata/'+ selectedLp, true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                var data = JSON.parse(xhr.responseText);
                if(data){

                    updateLps(data,selectedLp);
                    loaderLP.classList.add('d-none');
                    contentLP.classList.remove('d-none');
                }

            } else {
                console.error('Request failed with status:', xhr.status);
            }
        }
    };
    xhr.send();
}

function formatDate(dateString) {
    var date = new Date(dateString);
    var year = date.getFullYear();
    var month = String(date.getMonth() + 1).padStart(2, '0');
    var day = String(date.getDate()).padStart(2, '0');
    return year + '-' + month + '-' + day;
}

function updateSMModule(selectedGroup,selectedSM=null){
    var loaderSM = document.getElementById('loaderSM');
    var contentSM = document.getElementById('contentSM');
    loaderSM.classList.remove('d-none');
    contentSM.classList.add('d-none');
    var xhr = new XMLHttpRequest();
    xhr.open('GET', '/plateforme/groups/'+ selectedGroup +'/getsmdata/'+ selectedSM, true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                var data = JSON.parse(xhr.responseText);
                if(data){

                    updateSMModules(data,selectedSM);
                    loaderSM.classList.add('d-none');
                    contentSM.classList.remove('d-none');
                }

            } else {
                console.error('Request failed with status:', xhr.status);
            }
        }
    };
    xhr.send();
}

function updateSMModules(smStats, selectedSM=null){
    var sessionSM = document.getElementById('sessionSm');
    var cmiSM = document.getElementById('cmiSm');
    var tcSM = document.getElementById('tcSm');
    var trSM = document.getElementById('trSm');
    var insSMT = document.getElementById('insSmT');
    var insSMND = document.getElementById('insSmND');
    var insSMP = document.getElementById('insSmP');
    if(sessionSM != null){
        sessionSM.textContent = smStats.statSMTimes.total_session_time;
    }
    if(cmiSM != null){
        cmiSM.textContent = smStats.statSMTimes.total_cmi_time;
    }
    if(tcSM != null){
        tcSM.textContent = smStats.statSMTimes.total_calculated_time;
    }
    if(trSM != null){
        trSM.textContent = smStats.statSMTimes.total_recommended_time;
    }
    if(insSMT != null){
        insSMT.textContent = smStats.statSM.completed;
    }
    if(insSMND != null){
        insSMND.textContent = smStats.statSM.enrolled;;
    }
    if(insSMP != null){
        insSMP.textContent= smStats.statSM.in_progress;

    }

    $smsSelect = document.getElementById('select-sms');
    if($smsSelect != null){
        document.getElementById('select-sms').innerHTML="";
        document.getElementById('select-sms').insertAdjacentHTML('beforeend', '<option value="" class="text-gray-600">Séléctionner un module</option>');
        smStats.modulesSms.forEach(function(v) {
            var selected = v.docebo_id == selectedSM ? 'selected' : '';
            var content = '<option value="' + v.docebo_id + '"' + selected + '>' + v.name + '</option>';
            document.getElementById('select-sms').insertAdjacentHTML('beforeend', content);
        });
    }

    if(document.getElementById('chart-sm') != null){
        window.ApexCharts && (new ApexCharts(document.getElementById('chart-sm'), {
            chart: {
                type: "bar",
                fontFamily: 'inherit',
                height: 240,
                parentHeightOffset: 0,
                toolbar: {
                    show: false,
                },
                animations: {
                    enabled: false
                },
            },
            plotOptions: {
                bar: {
                    barHeight: '50%',
                    horizontal: true,
                    dataLabels: {
                        position: 'bottom'
                    },
                }
            },
            dataLabels: {
                enabled: true,
                textAnchor: 'start',
                style: {
                    colors: ['#000']
                },
                formatter: function (val, opt) {
                    return opt.w.globals.labels[opt.dataPointIndex] + ":  " + val + " inscriptions."
                },
                offsetX: 0,
                dropShadow: {
                    enabled: false
                }
            },
            stroke: {
                width: 1,
                colors: ['#fff']
            },
            series: [{
                name: "Total d'inscriptions",
                data: smStats.smCharts.data
            }],
            tooltip: {
                theme: 'dark',
                x: {
                    show: true
                },
                y: {
                    title: {
                        formatter: function () {
                        return ''
                        }
                    }
                }
            },
            grid: {
                padding: {
                    top: -20,
                    right: 0,
                    left: -4,
                    bottom: -4
                },
                strokeDashArray: 4,
            },
            xaxis: {
                categories: smStats.smCharts.labels,
            },
            yaxis: {
                labels: {
                    padding: 4,
                    show: false
                },
            },
            colors: ["#f4AAA4"],
            legend: {
                show: true,
            },
        })).render();
    }
}
