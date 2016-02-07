/** Changes charts **/
scpper.charts = {
    
    getDateGroupTooltip: function (date, group) {
        var period = '';            
        if (group === 'day') {
            period = date.format('UTC:mmm d, yyyy');
        } else if (group === 'week') {
            var nextDate = new Date(date);
            nextDate.setUTCDate(date.getUTCDate()+6);
            if (nextDate.getUTCFullYear() !== date.getUTCFullYear()) {
                period = date.format('UTC:mmm d, yyyy')+' - '+nextDate.format('UTC:mmm d, yyyy');
            } else if (nextDate.getUTCMonth() !== date.getUTCMonth()) {
                period = date.format('UTC:mmm d')+' - '+nextDate.format('UTC:mmm d')+nextDate.format(', yyyy');
            } else {
                period = date.format('UTC:mmm d')+' - '+nextDate.format('UTC:d')+nextDate.format(', yyyy');
            }                    
        } else if (group === 'month') {
            period = date.format('UTC:mmm, yyyy');
        } else {
            period = date.format('UTC:yyyy');
        }            
        return period;
    },

    getDateTooltipContent: function (date, group, text, value) {
        var period = scpper.charts.getDateGroupTooltip(date, group);
        return '<b>' + period + ' </b><br>' + text+': <b>' + value + '</b> <br>'
    },

    setFailedBackground: function (id)
    {
        var elem = document.getElementById(id);
        elem.className = "chart-failed";
    },

    removeChartContainer: function (id)
    {
        var elem = document.getElementById(id);
        elem.parentNode.removeChild(elem);
    }
};

scpper.charts.changes = {

    drawColumnChart: function (rawData, label, title, id, color) {
        var data = new google.visualization.DataTable();
        data.addColumn({type: 'date', label: 'Time'});
        data.addColumn({type: 'number', label: label});
        data.addColumn({type: 'string', role: 'tooltip', p: {html: true}});
        data.addRows(rawData);
        var options = {
            title: title,
            legend: 'none',
            chartArea: {width: '80%', height: '80%'},
            colors: [color],
            tooltip: { isHtml: true }
        };    
        var chart = new google.visualization.ColumnChart(document.getElementById(id));
        chart.draw(data, options);    
    },

    drawLineChart: function (rawData, label, title, id, color) {
        var data = new google.visualization.DataTable();
        data.addColumn({type: 'date', label: 'Time'});
        data.addColumn({type: 'number', label: label});
        data.addRows(rawData);
        var options = {
            title: title,
            legend: 'none',
            chartArea: {width: '80%', height: '80%'},
            colors: [color]
        };    
        var chart = new google.visualization.LineChart(document.getElementById(id));
        chart.draw(data, options);    
    },

    drawTimeLineCharts: function (result, barLabel, barTitle, barId, lineLabel, lineTitle, lineId, color) {
        // Define the chart to be drawn
        if (!result.success || result.data.length < 2) {
            scpper.charts.setFailedBackground(barId);
            scpper.charts.removeChartContainer(lineId);        
            return;
        }
        // Convert returned values from string to date
        for (var i=0; i<result.data.length; i++) {
            result.data[i][0] = scpper.convertDate(result.data[i][0]);
        }
        // Prepare data for bar chart
        var barData = $.extend(true, [], result.data);
        for (var i=0; i<barData.length; i++) {
            var group = result.group;            
            barData[i][2] = scpper.charts.getDateTooltipContent(barData[i][0], group, barLabel, barData[i][1]);
        }
        // Draw bar chart
        scpper.charts.changes.drawColumnChart(barData, barLabel, barTitle, barId, color);
        // Prepare data for line chart
        var lineData = $.extend(true, [], result.data);
        lineData[0][1] = lineData[0][1]+result.starting;
        for (var i=1; i<lineData.length; i++) {
            lineData[i][1] = lineData[i-1][1]+lineData[i][1];
        }
        // Draw line chart
        scpper.charts.changes.drawLineChart(lineData, lineLabel, lineTitle, lineId, color);
    },

    drawMemberCharts: function (chartsData) {
        $.ajax({
            url: "/changes/getMemberChartData",
            type: "get",
            data: chartsData
        })
        .done(function (result) {
            scpper.charts.changes.drawTimeLineCharts(result, 'Joined', 'New members', 'members-joined', 'Members', 'Total members', 'members-total', '#94C282');
        })
        .fail(function () {
            scpper.charts.setFailedBackground('members-joined');
            scpper.charts.removeChartContainer('members-total');
        });
    },

    drawPageCharts: function (chartsData) {
        $.ajax({
            url: "/changes/getPageChartData",
            type: "get",
            data: chartsData
        })
        .done(function (result) {
            scpper.charts.changes.drawTimeLineCharts(result, 'Created', 'New pages', 'pages-created', 'Pages', 'Total pages', 'pages-total', '#6DAECF');
        })
        .fail(function () {
            scpper.charts.setFailedBackground('pages-created');
            scpper.charts.removeChartContainer('pages-total');
        });
    },

    drawRevisionCharts: function (chartsData) {
        $.ajax({
            url: "/changes/getRevisionChartData",
            type: "get",
            data: chartsData
        })
        .done(function (result) {
            scpper.charts.changes.drawTimeLineCharts(result, 'Created', 'New revisions', 'revisions-created', 'Revisions', 'Total revisions', 'revisions-total', '#DB9191');
        })
        .fail(function () {
            scpper.charts.setFailedBackground('revisions-created');
            scpper.charts.removeChartContainer('revisions-total');
        });
    },

    drawVoteCharts: function (chartsData) {
        $.ajax({
            url: "/changes/getVoteChartData",
            type: "get",
            data: chartsData
        })
        .done(function (result) {
            scpper.charts.changes.drawTimeLineCharts(result, 'Votes', 'New votes', 'votes-cast', 'Votes', 'Total votes', 'votes-total', '#E0A96E');
        })
        .fail(function () {
            scpper.charts.setFailedBackground('votes-cast');
            scpper.charts.removeChartContainer('votes-total');
        });

    },

    go: function (chartsData) {
        if (chartsData.siteId < 0)
            return;
        scpper.charts.changes.drawMemberCharts(chartsData);
        scpper.charts.changes.drawPageCharts(chartsData);
        scpper.charts.changes.drawRevisionCharts(chartsData);
        scpper.charts.changes.drawVoteCharts(chartsData);
    }
};

/** Page rating chart **/

scpper.charts.page = {    
    
    convertDates: function (result) {
        // Convert returned values from string to date
        for (var i=0; i<result.votes.length; i++) {
            result.votes[i][0] = scpper.convertDate(result.votes[i][0]);
        }
        for (var i=0; i<result.revisions.length; i++) {
            result.revisions[i][0] = scpper.convertDate(result.revisions[i][0]);
        }                
    },
    
    prepareLineData: function (votes) {
        var rating = $.extend(true, [], votes);        
        for (var i=1; i<rating.length; i++) {
            rating[i][1] = rating[i-1][1]+rating[i][1];
        }
        var lineData = new google.visualization.DataTable();
        lineData.addColumn({type: 'date', label: 'Time'});
        lineData.addColumn({type: 'number', label: 'Rating'});
        lineData.addRows(rating);
        return lineData;
    },
    
    preparePointData: function (revisions) {
        var points = [];        
        for (var i=0; i<revisions.length; i++) {
            var annotationText = revisions[i][1].user.displayName+': "'+revisions[i][1].comments+'"';
            points.push([revisions[i][0], null, revisions[i][1].index.toString(), annotationText]);
        }
        // Draw line chart
        var pointData = new google.visualization.DataTable();
        pointData.addColumn({type: 'date', label: 'Time'});
        pointData.addColumn({type: 'number', label: 'Rating'});
        pointData.addColumn({type: 'string', role: 'annotation'});
        pointData.addColumn({type: 'string', role: 'annotationText', p: {html: true}});
        pointData.addRows(points);
        return pointData;
    },
    
    mergeData: function(lineData, pointData) {
        var data = google.visualization.data.join(lineData, pointData, 'full', [[0, 0], [1, 1]], [], [2, 3]);
        data.setValue(0, 1, 0);
        var last = 0;
        for (var i=1; i<data.getNumberOfRows(); i++) {
            if (data.getValue(i, 1)) {
                if (i-last > 1) {
                    for (var j=last+1; j<i; j++) {
                        var ratio = (data.getValue(j, 0)-data.getValue(last, 0))/(data.getValue(i, 0)-data.getValue(last, 0));
                        var interp = Math.round((data.getValue(last, 1)+(data.getValue(i, 1)-data.getValue(last, 1))*ratio));
                        data.setValue(j, 1, interp);
                    }
                }
                last = i;
            }
        }
        return data;
    },
    
    getOptions: function() {
        return {
            title: 'Rating',
            annotations: {
              textStyle: {
                fontSize: 14,
                bold: true,
                // The color of the text.
                color: '#DB9191'
              }
            },
            chartArea: {width: '80%', height: '80%'},
            legend: 'none',
            colors: ['#E0A96E'],      
        };            
    },
    
    processData: function (result) {
        // Define the chart to be drawn
        if (!result.success || result.votes.length < 2) {
            setFailedBackground('rating-chart');    
            return;
        }
        scpper.charts.page.convertDates(result);
        var lineData = scpper.charts.page.prepareLineData(result.votes);
        var pointData = scpper.charts.page.preparePointData(result.revisions);
        // Prepare data for line chart
        var data = scpper.charts.page.mergeData(lineData, pointData);
        var chart = new google.visualization.LineChart(document.getElementById('rating-chart'));
        var options = scpper.charts.page.getOptions();
        chart.draw(data, options);
    },    
    
    go: function (pageId) {
        // var callback = this.processData();
        $.ajax({
            url: "/page/ratingChart",
            type: "get",
            data: {pageId: pageId}
        })
        .done(function(result) {
            scpper.charts.page.processData(result);
        })
        .fail(function () {
            scpper.charts.setFailedBackground('rating-chart');
        });        
    }
};