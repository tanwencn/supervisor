<template>
  <div>
    <a-collapse accordion default-active-key="dir-tree-0" :bordered="false">
      <template #expandIcon="props">
        <a-icon type="caret-right" :rotate="props.isActive ? 90 : 0" />
      </template>
      <a-collapse-panel
        v-for="(item, i) in resolvers"
        :key="`dir-tree-${i}`"
        :header="item"
        :style="panelStyle"
      >
        <a-directory-tree
          :load-data="onLoad"
          v-if="tree[item]"
          :tree-data="tree[item]"
          @select="selectTree"
          :replace-fields="fields"
        />
        <a-empty v-else :description="false" />
      </a-collapse-panel>
    </a-collapse>
  </div>
</template>

<script>
export default {
  data() {
    return {
      panelStyle: "background: #fff;",
      resolvers: [],
      tree: {},
      fields: {
        key: "code",
        title: "basename",
      },
    };
  },
  mounted: function () {
    this.$http.get("/supervisor/api/resolvers").then((response) => {
      this.resolvers = response.data;
    });
  },
  watch: {
    resolvers(values) {
      values.forEach((item) => {
        this.tree[item] = [];
        this.request(item).then((data) => {
          this.tree[item] = data;
          this.tree = { ...this.tree };
        });
      });
    },
  },
  methods: {
    fileSize(size) {
      if (!size) return "";
      var num = 1024.0;
      if (size < num) return size + " B";
      if (size < Math.pow(num, 2)) return (size / num).toFixed(2) + " KB";
      if (size < Math.pow(num, 3))
        return (size / Math.pow(num, 2)).toFixed(2) + " MB";
      if (size < Math.pow(num, 4))
        return (size / Math.pow(num, 3)).toFixed(2) + " G";
      return (size / Math.pow(num, 4)).toFixed(2) + " T";
    },
    selectTree(keys, e) {
      var data = e.node.dataRef;
      this.$emit("descriptionsEvent", {
        path: data.path,
        type: data.type,
        size: this.fileSize(data.size),
        date: this.$moment(data.timestamp * 1000).format("YYYY-MM-DD HH:mm:ss"),
      });
      var code = keys[0];
      var isFile = e.node.isLeaf;
      var resolver = data.resolver;
      if (e.node.isLeaf)
        this.$router.push({ name: "content", query: { code, resolver } });
    },
    request(resolver, code = "") {
      return new Promise((resolve) => {
        this.$http
          .get("/supervisor/api/directoris", {
            params: { resolver, code },
          })
          .then((response) => {
            var data = response.data;
            resolve(data);
          });
      });
    },
    onLoad(node) {
      var resolver = node.dataRef.resolver;
      var code = node.dataRef.code;
      return new Promise((resolve) => {
        if (node.dataRef.children) {
          resolve();
          return;
        }
        this.request(resolver, code).then((data) => {
          node.dataRef.children = data;
          this.tree[resolver] = [...this.tree[resolver]];
          this.tree = { ...this.tree };
          resolve();
        });
      });
    },
  },
};
</script>
