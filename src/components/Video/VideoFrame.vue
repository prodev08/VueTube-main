<template>
	<div class="yt-video-wrapper">
		<iframe v-if="video.youtube_id" id="yt-player" type="text/html" :src="protocol+'//www.youtube.com/embed/'+video.youtube_id+'?enablejsapi=1'" frameborder="0" allowfullscreen></iframe>
	</div>
</template>

<script>
// https://www.npmjs.com/package/youtube-vue3

// import { YoutubeVue3 } from 'youtube-vue3'

// TODO: Update the player to a JS player instead of the iFrame player? A little more complex but should be faster and with more control...hopefully
export default {
	props: ['video'],
	components: {
		// YoutubeVue3
	},
	data() {
		return {
			// isPlaying: false,
			// player: null
			protocol: null
		};
	},
	created(){
		this.protocol = location.protocol;
	},
	computed: {
		
	},
	methods: {

	}
}
</script>

<style>
	.yt-video-wrapper {
		margin-top: 15px;
		width: 100%;
		padding-top: 56.25%;
		position: relative;
	}

	.yt-video-wrapper iframe {
		position: absolute;
		top: 0;
		left: 0;
		height: 100%;
		width: 100%;
	}

	@media (max-width: 1199px) {
		.yt-video-wrapper {
			margin-top: 0;
		}
	}
</style>