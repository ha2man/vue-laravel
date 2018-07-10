<template>
  <LineChart
    :width="width"
    :height="height"
    :chart-data="data"
    :options="options"
  />
</template>

<script>
export default {
  props: {
    width: {
      type: Number,
      default: 0
    },
    height: {
      type: Number,
      default: 0
    }
  },
  computed: {
    data() {
      return {
        labels: [
          "Jan",
          "Feb",
          "Mar",
          "Apr",
          "May",
          "Jun",
          "Jul",
          "Aug",
          "Sep",
          "Oct",
          "Nov",
          "Dec"
        ],
        datasets: [
          {
            label: "# of Votes",
            data: [0, 200, 250, 200, 500, 450, 850, 1050, 950, 1100, 900, 1200],
            borderWidth: 2,
            borderColor: "#3160D8",
            backgroundColor: "transparent",
            pointBorderColor: "transparent"
          },
          {
            label: "# of Votes",
            data: [0, 300, 400, 560, 320, 600, 720, 850, 690, 805, 1200, 1010],
            borderWidth: 2,
            borderDash: [2, 2],
            borderColor: "#BCBABA",
            backgroundColor: "transparent",
            pointBorderColor: "transparent"
          }
        ]
      };
    },
    options() {
      return {
        legend: {
          display: false
        },
        scales: {
          xAxes: [
            {
              ticks: {
                fontSize: "12",
                fontColor: this.$store.state.main.darkMode
                  ? "#718096"
                  : "#777777"
              },
              gridLines: {
                display: false
              }
            }
          ],
          yAxes: [
            {
              ticks: {
                fontSize: "12",
                fontColor: this.$store.state.main.darkMode
                  ? "#718096"
                  : "#777777",
                callback: function(value) {
                  return "$" + value;
                }
              },
              gridLines: {
                color: this.$store.state.main.darkMode ? "#718096" : "#D8D8D8",
                zeroLineColor: this.$store.state.main.darkMode
                  ? "#718096"
                  : "#D8D8D8",
                borderDash: [2, 2],
                zeroLineBorderDash: [2, 2],
                drawBorder: false
              }
            }
          ]
        }
      };
    }
  }
};
</script>
