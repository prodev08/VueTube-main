<template>
  <div id="admin" class="container-fluid">
    <div class="row">
      <div id="admin-menu" class="col-lg-3">
        <BaseButton
          @click="setSelectedTab('add-channel')"
          type="button"
          class="btn"
          :class="{
            'btn-secondary': selectedTab == 'add-channel',
            'btn-dark': selectedTab != 'add-channel'
          }"
          >Add Channel</BaseButton
        >
        <BaseButton
          @click="setSelectedTab('edit-channel')"
          type="button"
          class="btn"
          :class="{
            'btn-secondary': selectedTab == 'edit-channel',
            'btn-dark': selectedTab != 'edit-channel'
          }"
          >Edit Channel</BaseButton
        >
        <BaseButton
          @click="setSelectedTab('edit-categories')"
          type="button"
          class="btn"
          :class="{
            'btn-secondary': selectedTab == 'edit-categories',
            'btn-dark': selectedTab != 'edit-categories'
          }"
          >Edit Categories</BaseButton
        >
        <BaseButton
          @click="setSelectedTab('update-videos')"
          type="button"
          class="btn"
          :class="{
            'btn-secondary': selectedTab == 'update-videos',
            'btn-dark': selectedTab != 'update-videos'
          }"
          >Update Videos</BaseButton
        >
      </div>

      <div class="col-lg-9">
        <keep-alive>
          <component :is="selectedTab" :channels="channels"></component>
        </keep-alive>
      </div>
    </div>
  </div>
</template>

<script>
import AddChannel from './AddChannel.vue';
import EditChannel from './EditChannel.vue';
import EditCategories from './EditCategories.vue';
import UpdateVideos from './UpdateVideos.vue';

import BaseButton from '../UI/BaseButton.vue';

export default {
  props: [],
  components: {
    AddChannel,
    EditChannel,
    EditCategories,
    UpdateVideos,
    BaseButton
  },
  data() {
    return {
      selectedTab: 'add-channel',
      channels: {}
      //storedChannels: [],
      //page: 0,
      //channelsLoading: false
    };
  },
  provide() {
    return {
      //channels: this.storedChannels,
      //addChannel: this.addChannel,
      //removeChannel: this.removeChannel
    };
  },
  mounted() {
    // Setup our styles and topics if they are not already set
    this.loadChannels();
  },
  methods: {
    setSelectedTab(tab) {
      this.selectedTab = tab;
    },
    loadChannels() {
      fetch(process.env.VUE_APP_URL + 'channel/list.php', {
        //mode: 'no-cors',
        method: 'GET',
        headers: { 'Content-Type': 'application/json' }
      })
        .then(response => {
          if (response.ok) {
            return response.json();
          }
        })
        .then(data => {
          if (data.success) {
            this.channels = data.channels;
          }
        })
        .catch(error => {
          console.error('There was an error!', error);
        });
    }
  }
};
</script>

<style>
#admin {
  padding: 50px 0;
}

#admin-menu button {
  display: block;
  margin-bottom: 10px;
  width: 100%;
}

.card {
  margin-bottom: 30px;
}

.card .form-group {
  margin-bottom: 20px;
}

.card label {
  font-weight: bold;
}

.card .card-footer {
  background-color: white;
  padding: 30px 15px;
}
</style>
