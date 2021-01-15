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
                    >Click View</a
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
                width="80%"
                @cancel="onHide"
        >
      <pre style="white-space:pre-wrap; /* css3.0 */
white-space:-moz-pre-wrap; /* Firefox */
white-space:-pre-wrap; /* Opera 4-6 */
white-space:-o-pre-wrap; /* Opera 7 */
word-wrap:break-word; /* Internet Explorer 5.5+ */ ">{{ fullText }}</pre>
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
                var resolver = this.$route.query.resolver;
                var keys = keys.filter((item) => item != "fullText" && item != "id");

                keys.forEach((key) => {
                    var config = {
                        "search":true,
                        "content":null,
                        "width":null
                    };
                    Object.assign(config, window.supervisorConfig[resolver].table[key]);

                    var column = {
                        dataIndex: key,
                        title: key,
                        scopedSlots: {}
                    };

                    if (config.search) {
                        column.scopedSlots.filterDropdown = "filterDropdown";
                        column.scopedSlots.filterIcon = "filterIcon";
                        column.onFilter = (value, record) => {
                            let search = record[key]
                                .toString()
                                .toLowerCase()
                                .includes(value.toLowerCase());
                            if (search) return record;
                        };
                        column.onFilterDropdownVisibleChange = (visible) => {
                            if (visible) {
                                setTimeout(() => {
                                    this.searchInput.focus();
                                }, 0);
                            }
                        }
                    }
                    if (config.content == 'ellipsis') column.ellipsis = true;
                    if (config.content == 'click') column.scopedSlots.customRender = "action";
                    if (config.content == 'hide') return;
                    if (config.width) column.width = config.width;

                    console.log(column)
                    this.columns.push(column);
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
                return this.$http.get("/api/contents", {params});
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
                {...bootstrap},
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
