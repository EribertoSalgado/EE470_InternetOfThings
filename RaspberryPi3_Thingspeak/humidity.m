% Example 1 - humidity algorithm from temperature readings from thingspeak
% Replace this with channel ID to read data from:
readChannelID = <>;
% Enter your Read API Key between the '' below:
readAPIKey = '<>';
% Replace this with your channel ID to write data to:
writeChannelID =  <>;
% Enter the Write API Key between the '' below:
writeAPIKey = '<>';


%% Read Data %%
data = thingSpeakRead(readChannelID, 'ReadKey', readAPIKey);


%% Analyze Data %%
% Add code in this section to analyze data and store the result in the
% 'analyzedData' variable.
avgTemp = mean(data);
display(avgTemp,'Average Temperature');
stdTemp = std(data);
display(stdTemp,'Standard Deviation of Temperature')


%Plot the second field against time
[humidity,time] = thingSpeakRead(readChannelID,'Fields',2,'NumPoints',20);
plot(time,humidity,'r','LineWidth',2);
ylabel('Humidity');
title('My Humidity Station at SSU');
