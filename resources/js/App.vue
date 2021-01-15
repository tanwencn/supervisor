<template>
  <div id="app">
    <div style="padding: 30px 0px; width:90%; min-width: 1140px; margin: auto">
      <a-row :gutter="[16, 16]">
        <a-col :span="24">
          <a-page-header title="Laravel Supervisor">
            <template slot="extra">
            </template>
            <a-descriptions size="small" :column="3">
              <a-descriptions-item label="Last Modified" v-if="descriptions.date">
                {{ descriptions.date }}
              </a-descriptions-item>
              <a-descriptions-item label="Type" v-if="descriptions.type">
                {{ descriptions.type }}
              </a-descriptions-item>
              <a-descriptions-item label="Size" v-if="descriptions.size">
                {{ descriptions.size }}
              </a-descriptions-item>
              <a-descriptions-item label="Path" v-if="descriptions.path">
                {{ descriptions.path }}
              </a-descriptions-item>
            </a-descriptions>
          </a-page-header>
        </a-col>
      </a-row>
      <a-row type="flex" justify="space-around" align="top" :gutter="16">
        <a-col :span="5">
          <Directories @descriptionsEvent="descriptionsChange"></Directories>
        </a-col>
        <a-col :span="19">
          <router-view></router-view>
        </a-col>
      </a-row>
    </div>
    <a-back-top />
  </div>
</template>

<script>
import Directories from "./components/Directories.vue";

export default {
  name: "App",
  data() {
    return {
      descriptions: {},
    };
  },
  methods: {
    descriptionsChange(e) {
      this.descriptions = e;
    },
  },
  components: {
    Directories,
  },
};
</script>