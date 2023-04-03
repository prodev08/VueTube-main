<template>
	<div class="video-info">
		
		<h3>{{ video.title }}</h3>
		<p class="date">{{ videoDate }}</p>
		<div class="video-actions">
			<WatchLater :video="video" />
			<LikedIcon :video="video" />
		</div>
		<!-- <p v-if="tags" class="tags">
			<router-link :to="{ path: '/', query: { tag: tag.replace('#','') }}" v-for="tag in tags" :key="tag">{{ tag }}</router-link>
		</p> -->
		<h6 class="toggle-description" @click="toggleDescription">Toggle Description</h6>
		<div class="description" >
			
			<!-- <div class="video-description" v-if="!showDescription"><p>{{ video.short_description }}</p></div> -->
			<div class="video-description" v-html="video.description" v-if="showDescription"></div>
		</div>
	</div>
</template>


<script>
import { useToast } from "vue-toastification";

import moment from 'moment'

import LikedIcon from '../UI/LikedIcon.vue';
import WatchLater from '../UI/WatchLater.vue';


export default {
	props: ['video'],
	components: {
		LikedIcon,
		WatchLater
	},
	data() {
		return {
			showUserError: false,
			tags: '',
			toast: null,
			showDescription: false
		};
	},
	created() {
		this.tags = this.video.tags?this.video.tags.split(','):'';

		this.videoDate = moment(this.video.date).format('MMM D, YYYY');

		this.toast = useToast();
	},
	methods: {
		toggleDescription(){
			this.showDescription = !this.showDescription;
		},
		confirmError(){
			this.showUserError = false;
		}
	},
	watch:{
		video(newVideo){
			this.tags = newVideo.tags?newVideo.tags.split(','):'';
			this.videoDate = moment(newVideo.date).format('MMM D, YYYY');
		}
	}
}
</script>

<style scoped>
	.video-info {
		border-bottom: 1px solid #ccc;
		margin: 15px 0;
		padding-bottom: 15px;
		position: relative;
		padding-right: 80px;
	}

	.video-actions {
		font-size: 24px;
		position: absolute;
		top: 0;
		right: 15px;
	}

	.video-actions i {
		cursor: pointer;
		transition: color 0.2s linear;
		margin-left: 15px;
	}

	.toggle-description {
		cursor: pointer;	
		user-select: none;
	}

	body.darkmode p.date {
		color: #aaa;
	}

	.description {
		
	}

	.tags a {
		color: var(--bs-blue);
		margin-right: 10px;
	}

	@media (max-width: 1199px) {
		.video-info {
			padding: 0 90px 15px 0;
		}
		
		.video-actions {
			line-height: 1;
		}
	}
</style>