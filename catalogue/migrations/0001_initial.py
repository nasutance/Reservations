# Generated by Django 5.0.6 on 2024-06-18 13:27

from django.db import migrations, models


class Migration(migrations.Migration):

    initial = True

    dependencies = [
    ]

    operations = [
        migrations.CreateModel(
            name='Artist',
            fields=[
                ('artist_id', models.BigAutoField(auto_created=True, primary_key=True, serialize=False, verbose_name='artist_id')),
                ('firstname', models.CharField(max_length=60)),
                ('lastname', models.CharField(max_length=60)),
                    ],
            options={'db_table': 'artists',},
                                ),
                  ]
