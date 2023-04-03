<template>
	<i class="fas" @click.prevent="toggleLiked" :class="isLiked ? 'fa-heart-broken' : 'fa-heart'"></i>
</template>

<script>
import { mapGetters } from 'vuex';

import { useToast } from "vue-toastification";

export default {
	props: ['video'],
	data() {
		return {
			isLiked: false,
			toast: null,
		};
	},
	computed: {
		...mapGetters(["getGoogleUser"])
	},
	created(){
		this.toast = useToast();

		this.isLiked = this.video.isLiked?true:false;
	},
	methods: {
		toggleLiked() {
			if(this.getGoogleUser && this.getGoogleUser.accessToken){
				this.$store.dispatch('toggleLiked', { video: this.video });
				this.isLiked = !this.isLiked;

				if(! this.isLiked){
					this.$emit('unLiked', this.video);
				}
			}else{
				this.toast.error("You must be logged in to like a video!", this.$store.getters.getToastOptions);
			}
		},
	},
	watch:{
		video(newVal){
			this.isLiked = newVal.isLiked?true:false;
		}
	}
}
</script>

<style scoped>
	i {
		opacity: 1;
		transition: color 0.15s;
		color: var(--red);
	}

	/* i.fa-heart-broken.active {
		color: var(--red);
	} */
</style>