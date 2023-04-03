<template>
  <div id="edit-channel">
    <h2>Update Videos</h2>
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
    <BaseButton
      @click="updateVideos"
      :disabled="loading"
      class="btn btn-primary"
      >Update Videos</BaseButton
    >
    <LoadingIcon v-if="loading" />
  </div>
</template>

<script>
import BaseButton from '../UI/BaseButton.vue';
import LoadingIcon from '../UI/LoadingIcon.vue';

export default {
  props: ['channels'],
  components: {
    BaseButton,
    LoadingIcon
  },
  data() {
    return {
      selectedChannel: {
        title: '',
        channel_id: '',
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
      },
      loading: false
    };
  },
  methods: {
    populateChannel(event) {
      this.selectedChannel = this.channels[event.target.value];
    },
    updateVideos() {
      if (!this.selectedChannel) return;

      this.loading = true;

      fetch(process.env.VUE_APP_URL + 'channel/update-videos.php', {
        //mode: 'no-cors',
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ channel: this.selectedChannel })
      })
        .then(response => response.json())
        .then(data => {
          this.loading = false;
          if (data.success) {
            // inform user
          }
        })
        .catch(error => {
          this.loading = false;
          this.errorMessage = error;
          console.error('There was an error!', error);
        });
    }
  }
};
</script>

<style scoped></style>
