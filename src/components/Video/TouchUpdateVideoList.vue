<template>
  <div class="video-list">
    <div class="channel-controls">
      <a class="channel-control prev" @click.prevent="moveChannel(false)">
        <i class="fas fa-chevron-left"></i>
      </a>
      <a class="channel-control next" @click.prevent="moveChannel(true)">
        <i class="fas fa-chevron-right"></i>
      </a>
    </div>
    <div
      id="video-wrap"
      ref="draggable"
      :style="{
        width: width + 'px'
      }"
      @touchstart.passive="dragStart"
      @mousedown="dragStart"
    >
      <VideoCard v-for="video in videos" :key="video.video_id" :video="video" />
    </div>
  </div>
</template>

<script>
import VideoCard from './VideoCard';

/*
      @touchstart="handleTouchStart"
      @touchmove="handleTouchMove"
      @touchend="handleTouchEnd"
      @mousedown="handleTouchStart"
      @mousemove="handleTouchMove"
      @mouseup="handleTouchEnd"
*/

export default {
  props: ['videos'],
  components: {
    VideoCard
  },
  data() {
    return {
      // xDown: false,
      // yDown: false,
      // sliderSize: 340,
      // moveLeft: false,
      // moveRight: false,
      // translate: 0,
      // interval: 16,
      moveTranslate: 0,
      width: 6800,
      // $videoWrap: null,
      // videoPage: 1,
      // videosLoading: false,
      // maxWidth: 6800,
      // lastGesture: null,
      offset: null,
      dimensions: null,
      firstGesture: null,
      lastGesture: null,
      animationFrame: null,
      draggable: null,
      interval: 16
    };
  },
  mounted() {
    // this.$videoWrap = document.getElementById('video-wrap');
    // this.$videoWrap.addEventListener('touchmove', this.handleTouchMove, {passive: true})
    // this.$videoWrap.addEventListener('touchstart', this.handleTouchStart, {passive: true})
    // this.$videoWrap.addEventListener('touchend', this.handleTouchEnd, {passive: true})
    //this.$videoWrap.style.width = (this.videos.length * this.sliderSize)+"px";
    this.width = this.videos.length * this.sliderSize;

    this.draggable = this.$refs.draggable;
  },
  methods: {
    dragStart(e) {
      if (e.touches && e.touches.length > 1) return;

      var position = this.draggable.getBoundingClientRect();
      var pointer = e.touches ? e.touches[0] : e;

      this.offset = {
        left: pointer.pageX - position.left,
        top: pointer.pageY - position.top
      };

      this.dimensions = {
        width: position.width,
        height: position.height
      };

      this.firstGesture = this.lastGesture = {
        position: [pointer.pageX, pointer.pageY],
        timeStamp: e.timeStamp
      };

      window.addEventListener('mousemove', this.dragMove, false);
      window.addEventListener('mouseup', this.dragEnd, false);

      window.addEventListener('touchmove', this.dragMove, false);
      window.addEventListener('touchend', this.dragEnd, false);

      e.preventDefault();
    },
    dragMove(e) {
      var pointer = e.touches ? e.touches[0] : e;
      var posX = pointer.pageX - this.offset.left;
      var newGesture = {
        position: [pointer.pageX, pointer.pageY],
        timeStamp: e.timeStamp
      };

      if (e.timeStamp - this.lastGesture.timeStamp > this.interval) {
        newGesture.velocity = this.calculateVelocity(
          this.lastGesture,
          newGesture
        );
        newGesture.distance = this.calculateDistance(
          this.firstGesture,
          newGesture
        );
        newGesture.direction = this.calculateDirection(
          this.lastGesture,
          newGesture
        );

        this.lastGesture = newGesture;
      }

      if (posX > 0) {
        posX = 0;
      } else if (posX + this.dimensions.width > window.innerWidth) {
        posX = window.innerWidth - this.dimensions.width;
      }

      if (newGesture.distance > 0) {
        newGesture.distance = 0;
      }

      this.moveTranslate += newGesture.distance;

      let transformString = 'translate3d(' + this.moveTranslate + 'px, 0, 0)';

      this.animationFrame && window.cancelAnimationFrame(this.animationFrame);

      let draggable = this.draggable;
      this.animationFrame = window.requestAnimationFrame(function() {
        draggable.style.webkitTransform = draggable.style.transform = transformString;
      });

      e.preventDefault();
    },
    dragEnd() {
      this.animationFrame && window.cancelAnimationFrame(this.animationFrame);

      this.offset = this.dimensions = this.firstGesture = this.lastGesture = this.animationFrame = null;

      window.removeEventListener('mousemove', this.dragMove, false);
      window.removeEventListener('mouseup', this.dragEnd, false);

      window.removeEventListener('touchmove', this.dragMove, false);
      window.removeEventListener('touchend', this.dragEnd, false);
    },
    calculateDirection(start, end) {
      return start.position[0] - end.position[0];

      // var diffX = start.position[0] - end.position[0];
      // var diffY = start.position[1] - end.position[1];

      // return [
      //   Math.abs(diffX) > 0 ? (diffX > 0 ? 'left' : 'right') : 'N/A',
      //   Math.abs(diffY) > 0 ? (diffY > 0 ? 'up' : 'down') : 'N/A'
      // ];
    },
    calculateVelocity(start, end) {
      var deltaTime = end.timeStamp - start.timeStamp;
      var ratioX =
        (100 / window.innerWidth) * (start.position[0] - end.position[0]);

      return Math.abs(ratioX / deltaTime).toFixed(2);

      // var ratioX =
      //   (100 / window.innerWidth) * (start.position[0] - end.position[0]);
      // var ratioY =
      //   (100 / window.innerHeight) * (start.position[1] - end.position[1]);

      // return [
      //   Math.abs(ratioX / deltaTime).toFixed(2),
      //   Math.abs(ratioY / deltaTime).toFixed(2)
      // ];
    },
    calculateDistance(start, end) {
      var x = end.position[0] - start.position[0];
      //var y = end.position[1] - start.position[1];

      return x;
      //return Math.sqrt(x * x + y * y).toFixed(2);
    }

    //{
    // handleTouchStart(evt) {
    //   const firstTouch = evt.touches ? evt.touches[0] : evt;
    //   this.xDown = firstTouch.clientX;
    //   this.yDown = firstTouch.clientY;

    //   evt.preventDefault();
    //   evt.stopPropagation();
    // },
    // handleTouchEnd(evt) {
    //   // Direction
    //   // if (this.moveLeft) {
    //   //   this.moveChannel(true);
    //   // } else if (this.moveRight) {
    //   //   this.moveChannel(false);
    //   // }

    //   this.xDown = null;
    //   this.yDown = null;
    //   this.moveLeft = false;
    //   this.moveRight = false;

    //   evt.stopPropagation();
    // },
    // handleTouchMove(evt) {
    //   if (!this.xDown || !this.yDown) {
    //     return;
    //   }

    //   if (evt.timeStamp - this.lastGesture > this.interval) {
    //     this.lastGesture = evt.timeStamp;
    //   } else {
    //     return;
    //   }

    //   var distance = 30;
    //   const touchPoint = evt.touches ? evt.touches[0] : evt;

    //   var xUp = touchPoint.clientX;
    //   var yUp = touchPoint.clientY;

    //   var xDiff = this.xDown - xUp;
    //   var yDiff = this.yDown - yUp;

    //   if (Math.abs(xDiff) > Math.abs(yDiff)) {
    //     /*most significant*/

    //     if (xDiff > distance || xDiff < -distance) {
    //       console.log('xDiff: ' + xDiff);
    //       console.log('this.moveTranslate: ' + this.moveTranslate);
    //       this.moveTranslate -= xDiff;
    //       console.log('this.moveTranslate: ' + this.moveTranslate);
    //       if (this.moveTranslate > 0) {
    //         this.moveTranslate = 0;
    //       }
    //     }
    //   }
    // },
    // moveChannel(left) {
    //   let videosOnScreen = this.$el.clientWidth / this.sliderSize;
    //   let videosToShow = Math.floor(videosOnScreen);
    //   let maxTranslate = this.maxWidth - this.sliderSize * videosToShow;

    //   // Figure out how big our slide holder needs to be to contain all videos.
    //   if (this.videos && this.videos.length) {
    //     this.maxWidth = this.videos.length * this.sliderSize;
    //   }

    //   // Direction
    //   if (left) {
    //     this.translate -= this.sliderSize;
    //   } else {
    //     this.translate += this.sliderSize;
    //   }

    //   if (this.translate > 0) {
    //     this.translate = 0;
    //   } else if (Math.abs(this.translate) > maxTranslate) {
    //     this.translate = -maxTranslate;
    //   }

    //   this.moveTranslate = this.translate;
    // },
    // calculateDragDirection(start, end) {
    //   let diffX = start.position.x - end.position.x;
    //   let diffY = start.position.y - end.position.y;

    //   return {
    //     x: Math.abs(diffX) > 0 ? (diffX > 0 ? 'left' : 'right') : null,
    //     y: Math.abs(diffY) > 0 ? (diffY > 0 ? 'up' : 'down') : null
    //   };
    // },
    // calculateDragVelocity(start, end) {
    //   let deltaTime = end.timeStamp - start.timeStamp;
    //   let ratioX =
    //     (100 / window.innerWidth) * (start.position.x - end.position.x);
    //   let ratioY =
    //     (100 / window.innerHeight) * (start.position.y - end.position.y);

    //   return {
    //     x: Math.abs(ratioX / deltaTime),
    //     y: Math.abs(ratioY / deltaTime)
    //   };
    // },
    // calculateDragDistance(start, end) {
    //   let x = end.position.x - start.position.x;
    //   let y = end.position.y - start.position.y;

    //   return {
    //     x: x,
    //     y: y
    //   };
    // }
  }
};
</script>

<style scoped>
.video-list .card.video {
  width: 320px;
}

.video-list .card-img-back {
  width: 320px;
  height: 180px;
}

.video-list {
  white-space: nowrap;
  position: relative;
}

.video-list .card {
  margin-right: 20px;
}

.video-list .card:last-child {
  margin: 0;
}

#video-wrap {
  display: flex;
}

.channel-controls {
  position: absolute;
  bottom: 100%;
  right: 0;
  width: 100px;
}

.channel-control {
  cursor: pointer;
  display: inline-block;
  padding: 10px;
  margin: 0 10px;
  z-index: 1;
  width: 40px;
  top: 0;
  opacity: 0.5;
  text-align: center;
  transition: transform 0.2s, opacity 0.2s;
}

.channel-control:hover {
  opacity: 1;
  transform: scale(1.5);
}

.channel-control.prev {
  left: 0;
}

.channel-control.next {
  right: 0;
}

.channel-control i {
  font-size: 24px;
  transition: all 0.2s linear;
  color: black;
}

@media (max-width: 768px) {
  .channel-control {
    display: none;
  }
}
</style>
