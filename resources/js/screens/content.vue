<template>
  <div>
    <a-card :bordered="false" :bodyStyle="{ padding: '0' }">
      <a-table
        :columns="columns"
        :data-source="data"
        :pagination="pagination"
        row-key="id"
      >
        <a
          @click="() => onShow(record.fullText)"
          slot="action"
          slot-scope="record"
          >Full Text</a
        >
      </a-table></a-card
    >
    <a-modal
      :visible="show"
      :dialog-style="{ top: '20px' }"
      :footer="null"
      width="60%"
      @cancel="onHide"
    >
      <p>
        {{ fullText }}
      </p>
    </a-modal>
  </div>
</template>

<script>
const bootstrap = {
  first: true,
  columns: [],
  data: [],
  fullText: null,
  show: false,
};
export default {
  mounted: function () {
    this.load();
  },
  methods: {
    onHide() {
      this.fullText = null;
      this.show = false;
    },
    onShow(text) {
      this.fullText = text;
      this.show = true;
    },
    load() {
      var params = this.$route.query;
      params["reset"] = this.first ? 1 : 0;
      this.requestContent(params).then((response) => {
        var data = response.data;
        if (data.length == 0) return;
        this.first = false;
        if (this.columns.length < 1) {
          var keys = Object.keys(data[0]).filter((item) => item != "fullText");

          var columns = [];
          keys.forEach((key) => {
            if (key == "code") {
              columns.push({
                dataIndex: key,
                title: key,
                scopedSlots: { customRender: "code" },
                ellipsis: true,
              });
            } else {
              columns.push({
                dataIndex: key,
                title: key,
              });
            }
          });
          columns.push({
            title: "more",
            scopedSlots: { customRender: "action" },
          });
          this.columns = columns;
        }
        this.data = this.data.concat(data);
        this.load();
      });
    },
    requestContent(params) {
      return this.$http.get("/supervisor/api/contents", { params });
    },
    bootstrap() {
      for (var key in bootstrap) {
        this[key] = bootstrap[key];
      }
    },
  },
  watch: {
    $route(to, from) {
      this.bootstrap();
      this.load();
    },
  },
  data() {
    return Object.assign(
      { ...bootstrap },
      {
        pagination: {
          "show-total": (total) => `Total ${total} items`,
          style: "padding-right:15px",
        },
      }
    );
  },
};
</script>
