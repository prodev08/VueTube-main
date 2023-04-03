<template>
	<div id="liked-videos" class="row vertical-list">
		<div class="col-md-6">
			<h3>Liked</h3>
		</div>
		<div class="col-md-6 mb-4">
			<form class="form-inline " method="get" action="" @submit.prevent="">
				<div class="input-group">
					<input type="search" class="form-control" placeholder="Search Liked" aria-label="search" name="s" v-model.trim="search" @change="searchLiked()" />
					<div class="input-group-append">
						<i class="fas fa-cog fa-spin" v-if="likedLoading"></i>
						<i class="fas fa-search" @click="searchLiked()" v-else></i>
					</div>
				</div>
			</form>
		</div>
		<VideoCard v-for="video in likedVideos" 
			:key="video.video_id" 
			:video="video" 
			:class="{'col-md-4 col-lg-4 col-xl-3': true}" 
			:showLiked="true"
			@unLiked="unLiked" />
	</div>
</template>

<script>
import { mapGetters } from 'vuex';

import VideoCard from '../Video/VideoCard';

export default {
	props: ['videos'],
	components: {
		VideoCard
	},
	data() {
		return {
			likedLoading: false,
			likedPage: 0,
			likedVideos: [],
			search: '',
		};
	},
	computed: {
		...mapGetters(["getGoogleUser"])
	},
	mounted(){
		this.searchLiked();
	},
	methods: {
		unLiked(video){
			let found = this.likedVideos.indexOf(video);
			this.likedVideos.splice(found, 1);
		},
		searchLiked(){
			if(this.likedLoading) return;

			this.likedVideos = [];
			this.likedLoading = true;
			
			let searchString = '?'; //'?offset='+this.likedPage;

			// if(this.order){
			// 	searchString += '&offset='+this.likedPage+'&order='+this.order;
			// }else{
			// 	searchString += '&orderby=title&order=asc';
			// }

			if(this.search){
				searchString += '&s='+this.search.replace('#','');
			}

			if(this.getGoogleUser && this.getGoogleUser.accessToken){
				searchString += '&token='+this.getGoogleUser.accessToken;
			}

			fetch(process.env.VUE_APP_URL+'user/get-liked.php'+searchString, {
				//mode: 'no-cors',
				method: 'GET',
				headers: { 'Content-Type': 'application/json' }
			})
			.then(response => {
				if(response.ok){
					return response.json();
				}
			})
			.then(data => { 
				this.likedLoading = false;
				if(data.length){
					this.likedPage++;
					this.likedVideos = this.likedVideos.concat(data);
				}

			})
			.catch(error => {
				//this.errorMessage = error;
				this.likedLoading = false;
				console.error('There was an error!', error);
			});
		}
	},
}
</script>

<style>
	#liked-videos .card-img-back .fas.fa-clock {
		display: none;
	}
</style>