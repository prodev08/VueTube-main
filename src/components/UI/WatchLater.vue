<template>
	<i class="fas" @click.prevent="toggleWatchLater" :class="isSaved ? 'fa-check-circle' : 'fa-clock'"></i>
</template>


<script>
import { mapGetters } from 'vuex';

import { useToast } from "vue-toastification";

export default {
	props: ['video'],
	data() {
		return {
			isSaved: false,
			toast: null,
		};
	},
	computed: {
		...mapGetters(["getGoogleUser"])
	},
	created(){
		this.toast = useToast();
	
		this.isSaved = this.video.isSaved?true:false;
	},
	methods: {
		toggleWatchLater() {
			if(this.getGoogleUser && this.getGoogleUser.accessToken){
				this.$store.dispatch('toggleWatchLater', { video: this.video })
					.then(() => {
						this.isSaved = !this.isSaved;

						if(! this.isSaved){
							this.$emit('unSaved', this.video);
						}
					});

			}else{
				this.toast.error("You must be logged in to save a video!", this.$store.getters.getToastOptions);
			}
		},
	},
	watch:{
		video(newVal){
			this.isSaved = newVal.isSaved?true:false;
		}
	}
}
</script>

<style scoped>
	i.fas {
		opacity: 1;
		transition: color 0.15s;
	}

	i.fa-check-circle {
		color: var(--bs-light);
	}

	i.fa-clock {
		color: #aaa;
	}
</style>