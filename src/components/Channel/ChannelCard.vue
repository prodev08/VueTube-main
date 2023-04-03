<template>
  <div v-if="videos && videos.length > 0" class="channel">
    <div class="d-flex justify-content-between align-items-center">
      <div class="title-card">
        <ChannelInfo :channel="channel" />
      </div>
    </div>
    <VideoList :videos="videos" />
  </div>
</template>

<script>
import ChannelInfo from './ChannelInfo';
import VideoList from '../Video/VideoList';
//import VideoCard from '../Video/VideoCard';

export default {
  inject: [], // 'channels',
  props: ['channel'],
  components: {
    ChannelInfo,
    VideoList
    //VideoCard
  },
  data() {
    return {
      videos: [],
      videoPage: 1,
      videosLoading: false
    };
  },
  created() {
    this.videos = this.channel.videos;
  },
  methods: {}
};
</script>

<style scoped>
.channel {
  /* border-bottom: 1px solid #f7f7f9; */
  padding-bottom: 20px;
  margin-bottom: 50px;
  transition: border-color 0.2s;
}

.title-card {
  display: flex;
  align-items: center;
  justify-content: space-between;
}
</style>
