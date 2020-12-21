<template>
  <div>
    <a-spin
      :spinning="spinning"
      style="
        text-align: center;
        background: rgba(0, 0, 0, 0.05);
        border-radius: 4px;
        margin-bottom: 20px;
      "
    >
      <a-card :bordered="false" :bodyStyle="{ padding: '0' }">
        <a-spin
        v-if="!spinning"
          :spinning="loading"
          style="
            position: absolute;
            margin-left: 20px;
            margin-top: 20px;
            z-index: 4;
            display: block;
          "
        />
        <a-table
          :columns="columns"
          :data-source="data"
          :pagination="pagination"
          row-key="id"
        >
          <div
            slot="filterDropdown"
            slot-scope="{
              setSelectedKeys,
              selectedKeys,
              confirm,
              clearFilters,
              column,
            }"
            style="padding: 8px"
          >
            <a-input
              v-ant-ref="(c) => (searchInput = c)"
              :placeholder="`Search ${column.dataIndex}`"
              :value="selectedKeys[0]"
              style="width: 188px; margin-bottom: 8px; display: block"
              @change="
                (e) => setSelectedKeys(e.target.value ? [e.target.value] : [])
              "
              @pressEnter="
                () => handleSearch(selectedKeys, confirm, column.dataIndex)
              "
            />
            <a-button
              type="primary"
              icon="search"
              size="small"
              style="width: 90px; margin-right: 8px"
              @click="
                () => handleSearch(selectedKeys, confirm, column.dataIndex)
              "
            >
              Search
            </a-button>
            <a-button
              size="small"
              style="width: 90px"
              @click="() => handleReset(clearFilters)"
            >
              Reset
            </a-button>
          </div>
          <a-icon
            slot="filterIcon"
            slot-scope="filtered"
            type="search"
            :style="{ color: filtered ? '#108ee9' : undefined }"
          />

          <a slot="action" slot-scope="text" @click="() => onShow(text)"
            >Full Text</a
          >
          <template slot="highlight" slot-scope="text, record, index, column">
            <span v-if="searchText">
              <template
                v-for="(fragment, i) in text
                  .toString()
                  .split(
                    new RegExp(`(?<=${searchText})|(?=${searchText})`, 'i')
                  )"
              >
                <mark
                  v-if="fragment.toLowerCase() === searchText.toLowerCase()"
                  :key="i"
                  class="highlight"
                  >{{ fragment }}</mark
                >
                <template v-else>{{ fragment }}</template>
              </template>
            </span>
            <template v-else>
              {{ text }}
            </template>
          </template>
        </a-table>
      </a-card>
    </a-spin>
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
  spinning: true,
  loading: true,
  searchText: "",
  searchInput: null,
  searchedColumn: "",
};
export default {
  mounted: function () {
    this.bootstrap();
    this.load();
  },
  methods: {
    handleSearch(selectedKeys, confirm, dataIndex) {
      confirm();
      this.searchText = selectedKeys[0];
      this.searchedColumn = dataIndex;
    },

    handleReset(clearFilters) {
      clearFilters();
      this.searchText = "";
    },
    onHide() {
      this.fullText = null;
      this.show = false;
    },
    onShow(text) {
      this.fullText = text;
      this.show = true;
    },
    setColumns(keys) {
      var keys = keys.filter((item) => item != "fullText" && item != "id");
      keys.forEach((key) => {
        var column = {
          dataIndex: key,
          title: key,
          ellipsis: true,
          scopedSlots: { customRender: "highlight" },
        };

        this.columns.push(column);
      });
      this.columns.push({
        title: "more",
        dataIndex: "fullText",
        scopedSlots: {
          filterDropdown: "filterDropdown",
          filterIcon: "filterIcon",
          customRender: "action",
        },
        onFilter: (value, record) => {
          for (let key in record) {
            let search = record[key]
              .toString()
              .toLowerCase()
              .includes(value.toLowerCase());
            if (search) return record;
          }
        },
        onFilterDropdownVisibleChange: (visible) => {
          if (visible) {
            setTimeout(() => {
              this.searchInput.focus();
            }, 0);
          }
        },
      });
    },
    load() {
      var logTag = this.bootTag;
      var params = this.$route.query;
      params["reset"] = this.first ? 1 : 0;
      var tag = this.view_tag;
      this.requestContent(params).then((response) => {
        if (logTag != this.bootTag) return;
        this.spinning = false;
        var data = response.data;
        if (data.length == 0) return (this.loading = false);
        this.first = false;
        if (this.columns.length < 1) {
          this.setColumns(Object.keys(data[0]));
        }
        this.data = this.data.concat(data);
        this.load();
      });
    },
    requestContent(params) {
      return this.$http.get("/supervisor/api/contents", { params });
    },
    bootstrap() {
      this.bootTag = Math.ceil(Math.random() * 100000);
      for (var key in bootstrap) {
        this[key] = bootstrap[key];
      }
    },
  },
  watch: {
    $route(to, from) {
      //this.$router.push({ name: "blank" });
      this.bootstrap();
      this.load();
    },
  },
  data() {
    return Object.assign(
      { ...bootstrap },
      {
        pagination: {
          position: "top",
          "show-total": (total) => `Total ${total} items`,
          style: "padding-right:15px",
        },
      }
    );
  },
};
</script>
<style scoped>
.table-operations {
  margin-bottom: 16px;
}

.table-operations > button {
  margin-right: 8px;
}
</style>