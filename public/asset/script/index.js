$(document).ready(function () {
  $(".sideBarli").removeClass("activeLi");
  $(".indexSideA").addClass("activeLi");

  const initTotalUserChart = () => {
    const colors = $("#totalUsersChart").data("colors")?.split(",") || [
      "#3ece70",
    ];
    const chartElement = document.querySelector("#totalUsersChart");

    const chart = new ApexCharts(chartElement, {
      chart: { type: "area", height: 350 },
      stroke: { width: 3, curve: "smooth" },
      colors,
      dataLabels: {
        enabled: false,
        // formatter: function (value) {
        //     return value === 0 ? '' : value;
        // }
      },
      series: [{ name: "User Count", data: [] }],
      markers: { size: 0, style: "hollow" },
      xaxis: {
        type: "datetime",
        tickAmount: 6,
        labels: {
          datetimeUTC: false,
          format: "dd MMM",
        },
      },
      tooltip: {
        x: {
          format: "dd MMM yyyy",
        },
      },
      fill: {
        type: "gradient",
        gradient: {
          shadeIntensity: 1,
          opacityFrom: 0.7,
          opacityTo: 0,
          stops: [0, 100],
        },
      },
    });

    chart.render();

    const fetchAndUpdateChart = (timeRange) => {
      $.get(`${domainUrl}fetchUsersForChart`, function (response) {
        if (!response || !Array.isArray(response.data)) {
          console.error("No response or invalid data format.");
          return;
        }

        const today = new Date();
        let startDate = new Date(today);
        let endDate = new Date(today);

        // if (timeRange === "today") {
        //     startDate = new Date(today.toDateString()); // Start of today
        // } else
        if (timeRange === "seven_days") {
          startDate.setDate(today.getDate() - 6); // Last 7 days
        } else if (timeRange === "thirty_days") {
          startDate.setDate(today.getDate() - 29); // Last 30 days
        } else {
          startDate = null; // No range, use all data
        }

        const filledData = [];
        if (startDate) {
          let currentDate = new Date(startDate);
          while (currentDate <= endDate) {
            const dateKey = currentDate.toISOString().split("T")[0];
            const userEntry = response.data.find(
              (item) => item.date === dateKey
            );
            filledData.push({
              x: new Date(dateKey).getTime(), // Convert to timestamp
              y: userEntry ? userEntry.count : 0,
            });
            currentDate.setDate(currentDate.getDate() + 1);
          }
        } else {
          response.data.forEach((item) => {
            filledData.push({
              x: new Date(item.date).getTime(), // Convert to timestamp
              y: item.count,
            });
          });
        }

        chart.updateSeries([{ name: "User Count", data: filledData }]);

        if (startDate) {
          chart.updateOptions({
            xaxis: { min: startDate.getTime(), max: endDate.getTime() },
          });
        } else {
          chart.updateOptions({ xaxis: { min: undefined, max: undefined } });
        }
      });
    };

    fetchAndUpdateChart("thirty_days");

    const timeRanges = {
      // "#today": "today",
      "#seven_days": "seven_days",
      "#thirty_days": "thirty_days",
      "#allUsers": "allUsers",
    };

    Object.entries(timeRanges).forEach(([selector, timeRange]) => {
      document.querySelector(selector).addEventListener("click", (e) => {
        handleButtonActivation(e.target);
        fetchAndUpdateChart(timeRange);
      });
    });

    const handleButtonActivation = (activeButton) => {
      document
        .querySelectorAll(".toolbars button")
        .forEach((button) => button.classList.remove("active"));
      activeButton.classList.add("active");
    };
  };

  const initTotalPostChart = () => {
    const colors = $("#totalPostsChart").data("colors")?.split(",") || [
      "#3ece70",
    ];
    const chartElement = document.querySelector("#totalPostsChart");

    const chart = new ApexCharts(chartElement, {
      chart: { type: "area", height: 350 },
      stroke: { width: 3, curve: "smooth" },
      colors,
      dataLabels: {
        enabled: false,
      },
      series: [{ name: "User Posts", data: [] }],
      markers: { size: 0, style: "hollow" },
      xaxis: {
        type: "datetime",
        tickAmount: 6,
        labels: {
          datetimeUTC: false,
          format: "dd MMM",
        },
      },
      tooltip: {
        x: {
          format: "dd MMM yyyy",
        },
      },
      fill: {
        type: "gradient",
        gradient: {
          shadeIntensity: 1,
          opacityFrom: 0.7,
          opacityTo: 0,
          stops: [0, 100],
        },
      },
    });

    chart.render();

    const fetchAndUpdateChart = (timeRange) => {
      $.get(`${domainUrl}fetchPostsForChart`, function (response) {
        if (!response || !Array.isArray(response.data)) {
          console.error("No response or invalid data format.");
          return;
        }

        const today = new Date();
        let startDate = new Date(today);
        let endDate = new Date(today);

        if (timeRange === "seven_days_post") {
          startDate.setDate(today.getDate() - 6);
        } else if (timeRange === "thirty_days_post") {
          startDate.setDate(today.getDate() - 29);
        } else {
          startDate = null;
        }

        const filledData = [];
        if (startDate) {
          let currentDate = new Date(startDate);
          while (currentDate <= endDate) {
            const dateKey = currentDate.toISOString().split("T")[0];
            const userEntry = response.data.find(
              (item) => item.date === dateKey
            );
            filledData.push({
              x: new Date(dateKey).getTime(),
              y: userEntry ? userEntry.count : 0,
            });
            currentDate.setDate(currentDate.getDate() + 1);
          }
        } else {
          response.data.forEach((item) => {
            filledData.push({
              x: new Date(item.date).getTime(),
              y: item.count,
            });
          });
        }

        chart.updateSeries([{ name: "Post Count", data: filledData }]);

        if (startDate) {
          chart.updateOptions({
            xaxis: { min: startDate.getTime(), max: endDate.getTime() },
          });
        } else {
          chart.updateOptions({ xaxis: { min: undefined, max: undefined } });
        }
      });
    };

    fetchAndUpdateChart("thirty_days_post");

    const timeRanges = {
      "#seven_days_post": "seven_days_post",
      "#thirty_days_post": "thirty_days_post",
      "#all_posts": "all_posts",
    };

    Object.entries(timeRanges).forEach(([selector, timeRange]) => {
      document.querySelector(selector).addEventListener("click", (e) => {
        handleButtonActivation(e.target);
        fetchAndUpdateChart(timeRange);
      });
    });

    const handleButtonActivation = (activeButton) => {
      document
        .querySelectorAll(".toolbars-post button")
        .forEach((button) => button.classList.remove("active"));
      activeButton.classList.add("active");
    };
  };
  
  const initTotalReelChart = () => {
    const colors = $("#totalReelsChart").data("colors")?.split(",") || [
      "#3ece70",
    ];
    const chartElement = document.querySelector("#totalReelsChart");

    const chart = new ApexCharts(chartElement, {
      chart: { type: "area", height: 350 },
      stroke: { width: 3, curve: "smooth" },
      colors,
      dataLabels: {
        enabled: false,
      },
      series: [{ name: "User Reels", data: [] }],
      markers: { size: 0, style: "hollow" },
      xaxis: {
        type: "datetime",
        tickAmount: 6,
        labels: {
          datetimeUTC: false,
          format: "dd MMM",
        },
      },
      tooltip: {
        x: {
          format: "dd MMM yyyy",
        },
      },
      fill: {
        type: "gradient",
        gradient: {
          shadeIntensity: 1,
          opacityFrom: 0.7,
          opacityTo: 0,
          stops: [0, 100],
        },
      },
    });

    chart.render();

    const fetchAndUpdateChart = (timeRange) => {
      $.get(`${domainUrl}fetchReelsForChart`, function (response) {
        if (!response || !Array.isArray(response.data)) {
          console.error("No response or invalid data format.");
          return;
        }

        const today = new Date();
        let startDate = new Date(today);
        let endDate = new Date(today);

        if (timeRange === "seven_days_reel") {
          startDate.setDate(today.getDate() - 6);
        } else if (timeRange === "thirty_days_reel") {
          startDate.setDate(today.getDate() - 29);
        } else {
          startDate = null;
        }

        const filledData = [];
        if (startDate) {
          let currentDate = new Date(startDate);
          while (currentDate <= endDate) {
            const dateKey = currentDate.toISOString().split("T")[0];
            const userEntry = response.data.find(
              (item) => item.date === dateKey
            );
            filledData.push({
              x: new Date(dateKey).getTime(),
              y: userEntry ? userEntry.count : 0,
            });
            currentDate.setDate(currentDate.getDate() + 1);
          }
        } else {
          response.data.forEach((item) => {
            filledData.push({
              x: new Date(item.date).getTime(),
              y: item.count,
            });
          });
        }

        chart.updateSeries([{ name: "Reel Count", data: filledData }]);

        if (startDate) {
          chart.updateOptions({
            xaxis: { min: startDate.getTime(), max: endDate.getTime() },
          });
        } else {
          chart.updateOptions({ xaxis: { min: undefined, max: undefined } });
        }
      });
    };

    fetchAndUpdateChart("thirty_days_reel");

    const timeRanges = {
      "#seven_days_reel": "seven_days_reel",
      "#thirty_days_reel": "thirty_days_reel",
      "#all_reels": "all_reels",
    };

    Object.entries(timeRanges).forEach(([selector, timeRange]) => {
      document.querySelector(selector).addEventListener("click", (e) => {
        handleButtonActivation(e.target);
        fetchAndUpdateChart(timeRange);
      });
    });

    const handleButtonActivation = (activeButton) => {
      document
        .querySelectorAll(".toolbars-reel button")
        .forEach((button) => button.classList.remove("active"));
      activeButton.classList.add("active");
    };
  };
  
  const initTotalRoomChart = () => {
    const colors = $("#totalRoomsChart").data("colors")?.split(",") || [
      "#3ece70",
    ];
    const chartElement = document.querySelector("#totalRoomsChart");

    const chart = new ApexCharts(chartElement, {
      chart: { type: "area", height: 350 },
      stroke: { width: 3, curve: "smooth" },
      colors,
      dataLabels: {
        enabled: false,
      },
      series: [{ name: "User Rooms", data: [] }],
      markers: { size: 0, style: "hollow" },
      xaxis: {
        type: "datetime",
        tickAmount: 6,
        labels: {
          datetimeUTC: false,
          format: "dd MMM",
        },
      },
      tooltip: {
        x: {
          format: "dd MMM yyyy",
        },
      },
      fill: {
        type: "gradient",
        gradient: {
          shadeIntensity: 1,
          opacityFrom: 0.7,
          opacityTo: 0,
          stops: [0, 100],
        },
      },
    });

    chart.render();

    const fetchAndUpdateChart = (timeRange) => {
      $.get(`${domainUrl}fetchRoomsForChart`, function (response) {
        if (!response || !Array.isArray(response.data)) {
          console.error("No response or invalid data format.");
          return;
        }

        const today = new Date();
        let startDate = new Date(today);
        let endDate = new Date(today);

        if (timeRange === "seven_days_room") {
          startDate.setDate(today.getDate() - 6);
        } else if (timeRange === "thirty_days_room") {
          startDate.setDate(today.getDate() - 29);
        } else {
          startDate = null;
        }

        const filledData = [];
        if (startDate) {
          let currentDate = new Date(startDate);
          while (currentDate <= endDate) {
            const dateKey = currentDate.toISOString().split("T")[0];
            const userEntry = response.data.find(
              (item) => item.date === dateKey
            );
            filledData.push({
              x: new Date(dateKey).getTime(),
              y: userEntry ? userEntry.count : 0,
            });
            currentDate.setDate(currentDate.getDate() + 1);
          }
        } else {
          response.data.forEach((item) => {
            filledData.push({
              x: new Date(item.date).getTime(),
              y: item.count,
            });
          });
        }

        chart.updateSeries([{ name: "Room Count", data: filledData }]);

        if (startDate) {
          chart.updateOptions({
            xaxis: { min: startDate.getTime(), max: endDate.getTime() },
          });
        } else {
          chart.updateOptions({ xaxis: { min: undefined, max: undefined } });
        }
      });
    };

    fetchAndUpdateChart("thirty_days_room");

    const timeRanges = {
      "#seven_days_room": "seven_days_room",
      "#thirty_days_room": "thirty_days_room",
      "#all_rooms": "all_rooms",
    };

    Object.entries(timeRanges).forEach(([selector, timeRange]) => {
      document.querySelector(selector).addEventListener("click", (e) => {
        handleButtonActivation(e.target);
        fetchAndUpdateChart(timeRange);
      });
    });

    const handleButtonActivation = (activeButton) => {
      document
        .querySelectorAll(".toolbars-room button")
        .forEach((button) => button.classList.remove("active"));
      activeButton.classList.add("active");
    };
  };

  initTotalUserChart();
  initTotalPostChart();
  initTotalReelChart();
  initTotalRoomChart();


});
