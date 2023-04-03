<template>
  <div v-if="video.youtube_id" class="card video yt-video">
    <router-link :to="'/video/' + video.youtube_id" class="card-link">
      <div class="card-img-back">
        <WatchLater :video="video" @unSaved="unSave" />
        <LikedIcon v-if="showLiked" :video="video" @unLiked="unLiked" />
        <RemoveHistory
          v-if="showHistory"
          :video="video"
          @unHistory="unHistory"
        />
        <img
          loading="lazy"
          width="320"
          height="180"
          :src="
            'https://img.youtube.com/vi/' + video.youtube_id + '/mqdefault.jpg'
          "
          class="lazyload"
          :alt="video.title"
        />
        <!-- <i class="fas fa-play-circle"></i> -->
      </div>
    </router-link>
    <div class="card-body">
      <p class="ellipsis">{{ video.title }}</p>
      <router-link :to="'/channel/' + video.channel_youtube" v-if="showChannel">
        <span class="channel">{{ video.channel_title }}</span>
      </router-link>
      <!-- <span class="date watched" v-if="watchedDate"
        >Watched: {{ watchedDate }}</span
      >
      <span class="date watched" v-else-if="likedDate"
        >Liked: {{ likedDate }}</span
      > -->
      <span class="date">{{ videoTime }} | {{ videoDate }}</span>
    </div>
  </div>
</template>

<script>
// 'https://www.youtube.com/channel/'+video.channel_youtube
// :src="'https://img.youtube.com/vi/'+ video.youtube_id+'/sddefault.jpg'"
import moment from 'moment';

import LikedIcon from '../UI/LikedIcon.vue';
import RemoveHistory from '../UI/RemoveHistory.vue';
import WatchLater from '../UI/WatchLater.vue';

export default {
  props: ['video', 'showChannel', 'showLiked', 'showHistory'],
  components: {
    LikedIcon,
    RemoveHistory,
    WatchLater
  },
  data() {
    return {
      videoDate: null,
      watchedDate: null,
      likedDate: null,
      isSaved: false
    };
  },
  created() {
    this.videoDate = moment(this.video.date).format('MMM D, YYYY');
    //console.log(this.video.date + ' ' + this.video.time);
    this.videoTime = this.video.time; //.replace('00:', '');

    this.isSaved = this.video.isSaved ? true : false;

    if (this.video.watchedDate) {
      this.watchedDate = moment(this.video.watchedDate).format('MMM D, YYYY'); // h:mm a
    }

    if (this.video.likedDate) {
      this.likedDate = moment(this.video.likedDate).format('MMM D, YYYY'); //  h:mm a
    }
  },
  mounted() {
    //console.log(this.video);
  },
  computed: {},
  methods: {
    unSave(video) {
      this.$emit('unSaved', video);
    },
    unLiked(video) {
      this.$emit('unLiked', video);
    },
    unHistory(video) {
      this.$emit('unHistory', video);
    },
    updateVideo() {
      this.$store.dispatch('updateCurrentVideo', this.video.youtube_id);
    }
  }
};
</script>

<style scoped>
.card.video {
  border: 0;
  background-color: transparent;
  box-shadow: none;
  display: flex;
  transition: background-color 0.1s;
  height: 100%;
  margin-bottom: 15px;
}

.card.video:hover {
  text-decoration: none;
}

.card.video:hover .card-img-back i {
  opacity: 1;
}

.card-img-back {
  position: relative;
}

.card-img-back img {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: auto;
}

.card-img-back i {
  position: absolute;
  top: 5px;
  right: 5px;
  background-color: rgba(0, 0, 0, 0.5);
  color: rgba(255, 255, 255, 0.85);
  opacity: 0;
  transition: opacity 0.2s linear;
  z-index: 10;
  font-size: 24px;
  padding: 4px;
}

.card-img-back i.fa-check-circle {
  color: rgba(255, 255, 255, 1);
}

.card-img-back i.fa-primary {
  color: #373737;
}

.card-body {
  height: 96px;
  padding: 10px 0;
  position: relative;
  color: #55595c;
  transition: color 0.2s linear;
}

.card-body p {
  font-weight: 600;
  margin-bottom: 4px;
}

.card-body span.channel {
  margin-bottom: 0;
  font-size: 14px;
}

.card-body span.date {
  position: absolute;
  bottom: 10px;
  left: 0;
  font-size: 14px;
  transition: color 0.2s linear;
}

.darkmode .card-body span {
  color: #aaa;
}

@media (max-width: 768px) {
  .card-img-back i {
    opacity: 1;
  }
}
</style>
