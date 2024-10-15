<div wire:ignore.self x-ignore x-data="{ blocks: @entangle('blocks') }">
    <canvas id="myChart"></canvas>
    <script>
        const ctx = document.getElementById('myChart').getContext('2d');
        const myChart = new Chart(ctx, {
            type: @json($this->getType()),
            data: {
                labels: @json($this->getData()['labels']),
                datasets: [{
                    label: @json($this->getData()['datasets'][0]['label']),
                    data: @json($this->getData()['datasets'][0]['data']),
                    backgroundColor: @json($this->generateCategoryColors()),
                }]
            },
        });
    </script>
</div>