<template>
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-md-10">
				<div class="card">
					<div class="card-header">Audio List</div>

					<div class="card-body">
						<table class="table table-bordered">
							<thead>
							<tr>
								<th>#</th>
								<th>File Name</th>
								<th>Status</th>
								<th>Source</th>
								<th>Voice</th>
								<th>Music</th>
								<th>Actions</th>
							</tr>
							</thead>
							<tbody>
							<tr v-for="audio in list">
								<th>{{ audio.id }}</th>
								<th>{{ audio.name }}</th>
								<th>{{ audio.status }}</th>
								<th><a :href="audio.source">Download</a></th>
								<th><a :href="audio.vocals" v-if="audio.vocals">Download</a></th>
								<th><a :href="audio.accompaniment" v-if="audio.accompaniment">Download</a></th>
								<th>
									<button class="btn btn-danger btn-sm" @click="remove(audio)">REMOVE</button>
								</th>
							</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	export default {
		data() {
			return {
				list: [],
			}
		},
		mounted() {
			if(!window.authed) return;
			this.$root.$on('list.new', (row) => {
				this.list.push(row);
			});

			Echo.private(window.userChannel)
				.listen('.audio.status_update', (event) => {
					let listCount = this.list.length;
					for (let i = 0; i < listCount; i++) {
						let row = this.list[i];
						if (row.id == event.audio.id) {
							this.list[i] = event.audio;
							this.list.push(); // refresh the list
							break;
						}
					}
				});

			this.fetch()
		},
		methods: {
			fetch() {
				this.$http.get('/api/audio/')
					.then((result) => {
						this.list = result.data
					})
			},
			remove(audio) {
				this.$http.delete(`/api/audio/${audio.id}`)
					.then(() => {
						this.list.splice(this.list.map((r) => r.id).indexOf(audio.id), 1);
					}).catch(() => {
					alert('An error happened during process');
				})
			}
		}
	}
</script>
