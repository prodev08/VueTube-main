<template>
  <div class="position-relative">
    <div class="channel-controls">
      <a class="channel-control prev" @click.prevent="moveList(false)">
        <i class="fas fa-chevron-left"></i>
      </a>
      <a class="channel-control next" @click.prevent="moveList(true)">
        <i class="fas fa-chevron-right"></i>
      </a>
    </div>
    <div id="video-wrap" class="overflow-hidden">
      <div class="video-list row flex-nowrap m-0" ref="videoList">
        <VideoCard
          v-for="video in videos"
          :key="video.video_id"
          :video="video"
        />
      </div>
    </div>
  </div>
</template>

<script>
import VideoCard from './VideoCard';

export default {
  props: ['videos'],
  components: {
    VideoCard
  },
  data() {
    return {
      videosLoading: false,
      scrollDistance: 0, // how far to move the scrollbar per click
      currentScroll: 0,
      isScrolling: false,
      mousePos: {}
    };
  },
  mounted() {
    // Round the scroll distance to the nearest whole number of visible cards
    this.scrollDistance =
      Math.floor(this.$refs.videoList.clientWidth / 320) * 320;
  },
  methods: {
    moveList(next = true) {
      if (this.isScrolling) return;

      this.scrollDistance =
        Math.floor(this.$refs.videoList.clientWidth / 320) * 320;

      this.currentScroll = this.$refs.videoList.scrollLeft;
      this.isScrolling = true;
      setTimeout(() => {
        this.isScrolling = false;
      }, 500);

      if (next) {
        // Move right
        if (
          this.currentScroll >=
          this.$refs.videoList.scrollWidth - this.scrollDistance
        ) {
          // currentScroll location is greater than the width of the videolist. Set currentScroll to maximum scrollposition
          this.currentScroll =
            Math.floor(
              (this.$refs.videoList.scrollWidth - this.scrollDistance) /
                this.scrollDistance
            ) * this.scrollDistance;
        } else {
          // scroll right
          this.$refs.videoList.scrollTo({
            top: 0,
            left: (this.currentScroll += this.scrollDistance),
            behavior: 'smooth'
          });
        }
      } else {
        // Move left
        if (this.currentScroll <= 0) {
          // current scroll is <= 0 so set it to 0
          this.currentScroll = 0;
        } else {
          // scroll left
          this.$refs.videoList.scrollTo({
            top: 0,
            left: (this.currentScroll -= this.scrollDistance),
            behavior: 'smooth'
          });
        }
      }
    }
  }
};
</script>

<style scoped lang="scss">
.channel-controls {
  display: none;
}

.video-list {
  overflow-x: scroll;

  &::-webkit-scrollbar {
    display: block;
    width: 0.4rem;
  }
  &::-webkit-scrollbar-track {
    background-color: #ccc;
  }
  &::-webkit-scrollbar-thumb {
    background-color: #333;
    border-radius: 9999px;
  }

  .card.video {
    width: 260px;
  }
}

body.darkmode {
  .video-list {
    &::-webkit-scrollbar-track {
      background-color: #333333;
    }
    &::-webkit-scrollbar-thumb {
      background-color: #f1f1f1;
      border-radius: 9999px;
    }
  }
}

.video-channel-info h4 {
  font-size: 1.2rem;
}

@media (min-width: 600px) {
  /* Hide scrollbar for Chrome, Safari and Opera */
  /* .video-list::-webkit-scrollbar {
    display: none;
  } */

  .video-list .card.video {
    width: 320px;
  }

  .channel-controls {
    display: block;
    position: absolute;
    right: 0;
    bottom: 100%;

    .channel-control {
      cursor: pointer;
      display: inline-block;
      padding: 20px 10px;
      margin: 0 10px;
      z-index: 1;
      width: 40px;
      top: 0;
      opacity: 0.5;
      text-align: center;
      transition: transform 0.2s, opacity 0.2s;

      &:hover {
        opacity: 1;
        transform: scale(1.5);
      }

      &.prev {
        left: 0;
      }

      &.next {
        right: 0;
      }

      i {
        font-size: 24px;
        transition: all 0.2s linear;
      }
    }
  }
}
</style>
