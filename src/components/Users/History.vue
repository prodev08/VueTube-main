<template>
	<div id="history-videos" class="row vertical-list">
			<div class="col-md-6">
				<h3>History</h3>
			</div>
			<div class="col-md-6 mb-4">
				<form class="form-inline " method="get" action="" @submit.prevent="">
					<div class="input-group">
						<input type="search" class="form-control" placeholder="Search History" aria-label="search" name="s" v-model.trim="search" @change="searchHistory()" />
						<div class="input-group-append">
							<i class="fas fa-cog fa-spin" v-if="historyLoading"></i>
							<i class="fas fa-search" @click="searchHistory()" v-else></i>
						</div>
					</div>
				</form>
			</div>
		<VideoCard v-for="video in historyVideos" 
			:key="video.video_id" 
			:video="video" 
			:class="{'col-md-4 col-lg-4 col-xl-3': true}" 
			:showHistory="true"
			@unHistory="unHistory" />
	</div>
</template>

<script>
import VideoCard from '../Video/VideoCard';

import { mapGetters } from 'vuex';

export default {
	props: ['videos'],
	components: {
		VideoCard
	},
	data() {
		return {
			historyLoading: false,
			historyPage: 0,
			historyVideos: [],
			search: '',
			googleUser: null
		};
	},
	computed: {
		...mapGetters(["getGoogleUser"])
	},
	created(){
		//this.googleUser = this.$store.getters.getGoogleUser;

		this.searchHistory();
	},
	methods: {
		unHistory(video){
			let found = this.historyVideos.indexOf(video);
			this.historyVideos.splice(found, 1);
		},
		searchHistory(){
			if(this.historyLoading) return;
			
			this.historyVideos = [];
			this.historyLoading = true;
			
			let searchString = '?';// '?offset='+this.historyPage;

			if(this.order){
				searchString += '&orderby=date&order='+this.order;
			}else{
				searchString += '&orderby=date&order=asc';
			}

			if(this.search){
				searchString += '&s='+this.search.replace('#','');
			}

			if(this.getGoogleUser && this.getGoogleUser.accessToken){
				searchString += '&token='+this.getGoogleUser.accessToken;
			}

			fetch(process.env.VUE_APP_URL+'user/get-history.php'+searchString, {
				//mode: 'no-cors',
				method: 'GET',
				headers: { 'Content-Type': 'application/json' }
			})
			.then(response => {
				if(response.ok){
					this.channelsPage++;
					return response.json();
				}
			})
			.then(data => { 
				this.historyLoading = false;
				if(data.length){
					this.historyPage++;
					this.historyVideos = this.historyVideos.concat(data);
				}

			})
			.catch(error => {
				//this.errorMessage = error;
				this.historyLoading = false;
				console.error('There was an error!', error);
			});
		

			
		},
	},
}
</script>

<style>
	#history-videos .card-img-back .fas.fa-clock {
		display: none;
	}

</style>