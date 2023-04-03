<template>
  <div id="add-channel">
    <h2>Edit Channel</h2>
    <div class="mb-4">
      <select id="topic" class="form-control" @change="populateChannel">
        <option selected value="0">Select Channel to Edit</option>
        <option
          v-for="(channel, index) in channels"
          :key="channel.channel_id"
          :value="index"
          >{{ channel.title }}</option
        >
      </select>
    </div>
    <ChannelForm
      @channelAction="editChannel"
      :editChannel="selectedChannel"
      :action="'Update'"
    />
    <LoadingIcon v-if="insertLoading" />
  </div>
</template>

<script>
// TODO: Turn the loading icon into a floating absolute position icon
// TODO: Should we have a dropdown list of all channels? That might get crazy
import ChannelForm from './ChannelForm.vue';

import LoadingIcon from '../UI/LoadingIcon.vue';

export default {
  inject: [],
  props: ['channels'],
  components: {
    ChannelForm,
    LoadingIcon
  },
  data() {
    return {
      insertLoading: false,
      errorMessage: '',
      selectedChannel: {
        title: '',
        youtube_id: '',
        facebook: '',
        instagram: '',
        patreon: '',
        tiktok: '',
        twitter: '',
        twitch: '',
        website: '',
        //tags: '',
        styles: [],
        topics: []
        //focus: [],
      }
    };
  },
  created() {},
  mounted() {},
  methods: {
    populateChannel(event) {
      this.selectedChannel = this.channels[event.target.value];
    },
    editChannel(channel) {
      this.insertLoading = true;

      fetch(process.env.VUE_APP_URL + 'channel/edit.php', {
        //mode: 'no-cors',
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ channel: channel })
      })
        .then(response => response.json())
        .then(data => {
          this.insertLoading = false;
          if (data.success) {
            // inform user
          }
        })
        .catch(error => {
          this.insertLoading = false;
          this.errorMessage = error;
          console.error('There was an error!', error);
        });
    }
  }
};
</script>

<style scoped>
.darkmode .loading-icon {
  color: #111111;
}
</style>
